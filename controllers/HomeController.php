<?php
/**
 * Home Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/NailDesign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../config/app.php';

class HomeController extends BaseController {
    public function index() {
        $designModel = new NailDesign();
        $categoryModel = new Category();
        $serviceModel = new Service();
        $reviewModel = new Review();

        $featuredDesigns = array_slice($designModel->getActive(), 0, 6);
        $categories = $categoryModel->getActive();
        $services = $serviceModel->getActive();
        $featuredReviews = $reviewModel->getFeatured(6);
        $avgRating = $reviewModel->getAverageRating();

        // LocalBusiness structured data — Google indexes the salon as a real,
        // findable place with services, hours and contact.
        $config = require __DIR__ . '/../config/app.php';
        $biz = $config['business'];
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'NailSalon',
            'name' => $biz['name'],
            'url' => rtrim(base_url(), '/') ?: $biz['url'],
            'telephone' => $biz['phone'],
            'email' => $biz['email'],
            'priceRange' => $biz['price_range'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $biz['address']['street'],
                'addressLocality' => $biz['address']['locality'],
                'addressRegion' => $biz['address']['region'],
                'postalCode' => $biz['address']['postal_code'],
                'addressCountry' => $biz['address']['country'],
            ],
            'geo' => $biz['geo'] ? ['@type' => 'GeoCoordinates', 'latitude' => $biz['geo']['lat'], 'longitude' => $biz['geo']['lng']] : null,
            'openingHoursSpecification' => array_map(function ($range) {
                [$days, $hours] = explode(' ', $range, 2);
                [$open, $close] = explode('-', $hours);
                return [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => array_map(function ($d) {
                        return 'https://schema.org/' . str_replace(['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'], ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], $d);
                    }, explode(',', $days)),
                    'opens' => $open . ':00',
                    'closes' => $close . ':00',
                ];
            }, $biz['openingHours']),
            'sameAs' => array_values(array_filter($biz['social'])),
        ];

        $this->view('home/index', [
            'featuredDesigns' => $featuredDesigns,
            'categories' => $categories,
            'services' => $services,
            'featuredReviews' => $featuredReviews,
            'avgRating' => $avgRating,
            'jsonLd' => $jsonLd,
            'meta' => [
                'title' => 'Tips by Nadine - Nail Salon & Art Studio | Book Online',
                'description' => 'Book serene nail care online at Tips by Nadine — gel manicures, acrylics, pedicures and nail art with smart booking and AI design recommendations in a serene studio.',
                'og_type' => 'website',
            ],
            'pageRoute' => '',
            'pageTitle' => 'Home - Tips by Nadine',
        ]);
    }

    public function gallery() {
        $designModel = new NailDesign();
        $categoryModel = new Category();

        $categoryId = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';

        if ($categoryId) {
            $designs = $designModel->getByCategory($categoryId);
        } elseif ($search) {
            $designs = $designModel->findAll(['is_active' => true]);
            $designs = array_filter($designs, function($d) use ($search) {
                return stripos($d['name'], $search) !== false ||
                       stripos($d['description'], $search) !== false;
            });
        } else {
            $designs = $designModel->getActive();
        }

        $categories = $categoryModel->getActive();

        $this->view('home/gallery', [
            'designs' => $designs,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchTerm' => $search,
            'meta' => [
                'title' => 'Nail Design Gallery - Tips by Nadine',
                'description' => 'Browse the Tips by Nadine nail design gallery — classic, french tip, glitter, floral, geometric and seasonal nail art, each with transparent pricing. Book what you love online.',
                'og_type' => 'website',
            ],
            'pageRoute' => 'gallery.php',
            'pageTitle' => 'Nail Design Gallery - Tips by Nadine',
        ]);
    }

    public function designDetail($id) {
        $designModel = new NailDesign();
        $design = $designModel->find($id);

        if (!$design || !$design['is_active']) {
            http_response_code(404);
            $this->view('errors/404', ['pageTitle' => 'Page Not Found - Tips by Nadine']);
            return;
        }

        // Reviews for this design as social proof on the detail page.
        $reviewModel = new Review();
        $designReviews = $reviewModel->getForDesign($id, 4);
        $avgRating = $reviewModel->getAverageRating();

        $this->view('home/design-detail', [
            'design' => $design,
            'designReviews' => $designReviews,
            'avgRating' => $avgRating,
            'meta' => [
                'title' => $design['name'] . ' - Tips by Nadine',
                'description' => 'Book the "' . $design['name'] . '" nail design at Tips by Nadine' . ($design['description'] ? ' — ' . implode(' ', array_slice(explode(' ', $design['description']), 0, 18)) : '') . '. See pricing and reserve your appointment online.',
                'og_type' => 'product',
                'image' => $design['image_path'] ? base_url($design['image_path']) : null,
            ],
            'pageRoute' => 'design.php?id=' . $id,
            'pageTitle' => $design['name'] . ' - Tips by Nadine',
        ]);
    }

    public function about() {
        $this->view('home/about', [
            'meta' => [
                'title' => 'About Tips by Nadine - Our Story & Values',
                'description' => 'Meet Tips by Nadine — a serene nail studio pairing skilled artistry with a calm, tropical atmosphere. Learn how quality, creative design and client-first care shape every visit.',
                'og_type' => 'website',
            ],
            'pageRoute' => 'about.php',
            'pageTitle' => 'About Us - Tips by Nadine',
        ]);
    }

    public function contact() {
        $contactMeta = [
            'title' => 'Contact Us - Tips by Nadine',
            'description' => 'Reach Tips by Nadine nail studio — send a message, call, or visit us in Manila. We answer every enquiry quickly.',
            'og_type' => 'website',
        ];
        $contactRoute = 'contact.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            $errors = [];

            if (empty($name)) $errors[] = 'Please enter your name.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
            if (empty($subject)) $errors[] = 'Please enter a subject.';
            if (empty($message)) $errors[] = 'Please enter a message.';

            if (empty($errors)) {
                // Log the contact message for the owner. In production this
                // would send an email; locally it is stored so the owner can read it.
                $contactLog = dirname(__DIR__) . '/logs/contact_messages.log';
                if (!is_dir(dirname($contactLog))) {
                    mkdir(dirname($contactLog), 0755, true);
                }
                $line = sprintf(
                    "[%s] %s <%s> - %s: %s" . PHP_EOL,
                    date('Y-m-d H:i:s'),
                    $name,
                    $email,
                    $subject,
                    str_replace(["\r", "\n"], ' ', $message)
                );
                file_put_contents($contactLog, $line, FILE_APPEND);

                $this->flash('success', 'Thank you! Your message has been sent. We\'ll get back to you soon.');
                $this->redirect('/contact.php');
            }

            $this->view('home/contact', [
                'meta' => $contactMeta,
                'pageRoute' => $contactRoute,
                'pageTitle' => 'Contact Us - Tips by Nadine',
                'errors' => $errors,
                'old' => $_POST,
            ]);
            return;
        }

        $this->view('home/contact', [
            'meta' => $contactMeta,
            'pageRoute' => $contactRoute,
            'pageTitle' => 'Contact Us - Tips by Nadine',
        ]);
    }
}