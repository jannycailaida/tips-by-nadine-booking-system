<?php
/**
 * AI Recommendation Service
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../config/app.php';

class AIRecommendationService {
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/app.php';
    }

    public function getRecommendations($referenceImagePath, $availableDesigns) {
        if (!$this->config['ai']['enabled'] || empty($this->config['ai']['api_endpoint'])) {
            return $this->getFallbackRecommendations($availableDesigns);
        }

        try {
            // Call AI service API
            $response = $this->callAIService($referenceImagePath, $availableDesigns);
            return $response;
        } catch (Exception $e) {
            error_log("AI Recommendation failed: " . $e->getMessage());
            return $this->getFallbackRecommendations($availableDesigns);
        }
    }

    private function callAIService($imagePath, $designs) {
        // Prepare design data for AI
        $designData = array_map(function($design) {
            return [
                'id' => $design['id'],
                'name' => $design['name'],
                'category' => $design['category_name'] ?? 'Unknown',
                'description' => $design['description'],
                'price' => $design['price'],
            ];
        }, $designs);

        $payload = [
            'reference_image' => $imagePath,
            'available_designs' => $designData,
            'max_recommendations' => 3,
        ];

        $ch = curl_init($this->config['ai']['api_endpoint']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config['ai']['api_key'],
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("AI service returned HTTP $httpCode");
        }

        $result = json_decode($response, true);
        return $this->formatRecommendations($result, $designs);
    }

    private function formatRecommendations($aiResult, $allDesigns) {
        $recommendations = [];

        if (isset($aiResult['recommendations']) && is_array($aiResult['recommendations'])) {
            foreach ($aiResult['recommendations'] as $rec) {
                $design = $this->findDesignById($rec['design_id'], $allDesigns);
                if ($design) {
                    $recommendations[] = [
                        'design_id' => $design['id'],
                        'name' => $design['name'],
                        'category' => $design['category_name'] ?? 'Unknown',
                        'price' => $design['price'],
                        'image_path' => $design['image_path'],
                        'confidence' => $rec['confidence'] ?? 0,
                        'reason' => $rec['reason'] ?? 'AI suggested match',
                    ];
                }
            }
        }

        // If AI didn't return enough, fill with fallback
        if (count($recommendations) < 3) {
            $fallback = $this->getFallbackRecommendations($allDesigns);
            foreach ($fallback as $fb) {
                if (count($recommendations) >= 3) break;
                $exists = false;
                foreach ($recommendations as $r) {
                    if ($r['design_id'] === $fb['design_id']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $recommendations[] = $fb;
                }
            }
        }

        return $recommendations;
    }

    private function findDesignById($id, $designs) {
        foreach ($designs as $design) {
            if ($design['id'] == $id) {
                return $design;
            }
        }
        return null;
    }

    private function getFallbackRecommendations($designs) {
        // Simple fallback: return random active designs
        $activeDesigns = array_filter($designs, function($d) {
            return !empty($d['image_path']);
        });

        if (empty($activeDesigns)) {
            return [];
        }

        shuffle($activeDesigns);
        $selected = array_slice($activeDesigns, 0, 3);

        return array_map(function($design) {
            return [
                'design_id' => $design['id'],
                'name' => $design['name'],
                'category' => $design['category_name'] ?? 'Unknown',
                'price' => $design['price'],
                'image_path' => $design['image_path'],
                'confidence' => 0.5,
                'reason' => 'Popular choice',
            ];
        }, $selected);
    }
}