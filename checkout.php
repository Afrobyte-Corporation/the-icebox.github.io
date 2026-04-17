<?php
/**
 * IceBox Frozen Treats — Shopping Cart & Checkout
 * 
 * SETUP INSTRUCTIONS:
 * 1. Install Stripe PHP library:
 *    composer require stripe/stripe-php
 * 
 * 2. Create a .env file (never commit this) with:
 *    STRIPE_SECRET_KEY=sk_live_...
 *    STRIPE_PUBLISHABLE_KEY=pk_live_...
 *    STRIPE_WEBHOOK_SECRET=whsec_...
 * 
 * 3. For Apple Pay, verify your domain with Stripe:
 *    https://dashboard.stripe.com/settings/payment_methods
 *    Download the domain verification file and place it at:
 *    /.well-known/apple-developer-merchantid-domain-association
 * 
 * 4. For production, use HTTPS — required for Apple Pay and Stripe.
 */

session_start();

// Auto-detects HTTP locally and HTTPS on live server
$baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
    ? 'https://' . $_SERVER['HTTP_HOST']
    : 'http://'  . $_SERVER['HTTP_HOST'];


// ─── Load Stripe ──────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables (use vlucas/phpdotenv or set directly)
// If you don't have phpdotenv, replace these with your actual keys:
$stripeSecretKey      = 'sk_test_51THwDURjHxcYaClNZwUVjoEJdnfOTnm89Tu9ELZQ0IRJuJPydxQN6vqbDo8dHeI1q5WaMLgfpl0TBLcefbPoSNJr0016nNLUXk';
$stripePublishableKey = 'pk_test_51THwDURjHxcYaClN3sWslxu6IZ7ClbYipgARgz5NKAaORkL2ncoB368TXVFJCr0VByXKTGWuSRlDHNdZsHnTASZH00dOHqDQyD';
$stripeWebhookSecret  = 'whsec_0zfEYRGVmQmcS09cU0qOzh8LKxveWfR9';
\Stripe\Stripe::setApiKey($stripeSecretKey);

// ─── Products ─────────────────────────────────────────────────
// In a real app, load these from a database.
// Prices are in cents (USD): $3.50 = 350
// ─────────────────────────────────────────────────────────────
// AVAILABLE FLAVORS (shown in descriptions)
// ─────────────────────────────────────────────────────────────
// ICE CREAM FLAVORS (11):
//   Vanilla · Strawberry · Chocolate · Cookies & Cream
//   Mint Chocolate Chip · Butter Pecan · Coffee
//   Strawberry Cheesecake · Birthday Cake
//   Black Sweet Cherry · Sugar Free Vanilla
//
// WATER ICE FLAVORS (15):
//   Mango · Lemon · Cherry · Blueberry · Pineapple
//   Candy Fish · Watermelon · Pina Colada · Root Beer · Cotton Candy
//   Tropical Rainbow · Blue Hawaiian · Sour Rainbow
//   Strawberry Lemonade · Sugar Free Cherry
//
// MILKSHAKE FLAVORS (4):
//   Chocolate · Vanilla · Strawberry · Oreo
// ─────────────────────────────────────────────────────────────

$iceCreamFlavors = 'Flavors: Vanilla, Strawberry, Chocolate, Cookies & Cream, Mint Chocolate Chip, Butter Pecan, Coffee, Strawberry Cheesecake, Birthday Cake, Black Sweet Cherry, Sugar Free Vanilla.';
$waterIceFlavors = 'Flavors: Mango, Lemon, Cherry, Blueberry, Pineapple, Candy Fish, Watermelon, Pina Colada, Root Beer, Cotton Candy, Tropical Rainbow, Blue Hawaiian, Sour Rainbow, Strawberry Lemonade, Sugar Free Cherry.';
$milkshakeFlavors = 'Flavors: Chocolate, Vanilla, Strawberry, Oreo.';

$products = [

    // ══════════════════════════════════════════════════════════
    // WATER ICE
    // Kids $1.50 · Small $2.50 · Medium $4.00 · Large $6.00 · X-Large $9.00
    // ══════════════════════════════════════════════════════════
    'waterice_kids' => [
        'name'        => 'Water Ice — Kids',
        'description' => "Kids size water ice cup. $waterIceFlavors",
        'price'       => 150,
        'image'       => 'rainbowwi.png',
        'category'    => 'Water Ice',
        'size'        => 'Kids',
    ],
    'waterice_sm' => [
        'name'        => 'Water Ice — Small',
        'description' => "Small water ice cup. $waterIceFlavors",
        'price'       => 250,
        'image'       => 'pinaandmangowi.png',
        'category'    => 'Water Ice',
        'size'        => 'Small',
    ],
    'waterice_md' => [
        'name'        => 'Water Ice — Medium',
        'description' => "Medium water ice cup. $waterIceFlavors",
        'price'       => 400,
        'image'       => 'cttn.png',
        'category'    => 'Water Ice',
        'size'        => 'Medium',
    ],
    'waterice_lg' => [
        'name'        => 'Water Ice — Large',
        'description' => "Large water ice cup. $waterIceFlavors",
        'price'       => 600,
        'image'       => 'strawberryndcherry.png',
        'category'    => 'Water Ice',
        'size'        => 'Large',
    ],
    'waterice_xl' => [
        'name'        => 'Water Ice — X-Large',
        'description' => "X-Large water ice cup. $waterIceFlavors",
        'price'       => 900,
        'image'       => 'cttnandmang.png',
        'category'    => 'Water Ice',
        'size'        => 'X-Large',
    ],

    // ══════════════════════════════════════════════════════════
    // GELATI
    // Small $4.50 · Medium $5.00 · Large $6.50 · X-Large $10.00
    // ══════════════════════════════════════════════════════════
    'gelati_sm' => [
        'name'        => 'Gelati — Small',
        'description' => "Water ice layered with soft serve ice cream. Small size. Choose any water ice flavor + any ice cream flavor. Water Ice: $waterIceFlavors Ice Cream: $iceCreamFlavors",
        'price'       => 450,
        'image'       => 'cottoncandygelatiwjimmys.png',
        'category'    => 'Gelati',
        'size'        => 'Small',
    ],
    'gelati_md' => [
        'name'        => 'Gelati — Medium',
        'description' => "Water ice layered with soft serve ice cream. Medium size. Choose any water ice flavor + any ice cream flavor. Water Ice: $waterIceFlavors Ice Cream: $iceCreamFlavors",
        'price'       => 500,
        'image'       => 'mangogelati.png',
        'category'    => 'Gelati',
        'size'        => 'Medium',
    ],
    'gelati_lg' => [
        'name'        => 'Gelati — Large',
        'description' => "Water ice layered with soft serve ice cream. Large size. Choose any water ice flavor + any ice cream flavor. Water Ice: $waterIceFlavors Ice Cream: $iceCreamFlavors",
        'price'       => 650,
        'image'       => 'doublegelati.png',
        'category'    => 'Gelati',
        'size'        => 'Large',
    ],
    'gelati_xl' => [
        'name'        => 'Gelati — X-Large',
        'description' => "Water ice layered with soft serve ice cream. X-Large size. Choose any water ice flavor + any ice cream flavor. Water Ice: $waterIceFlavors Ice Cream: $iceCreamFlavors",
        'price'       => 1000,
        'image'       => 'trop.png',
        'category'    => 'Gelati',
        'size'        => 'X-Large',
    ],

    // ══════════════════════════════════════════════════════════
    // SOFT SERVE ICE CREAM
    // Small $4.00 · Medium $5.00 · Large $7.00
    // ══════════════════════════════════════════════════════════
    'softserve_sm' => [
        'name'        => 'Soft Serve Ice Cream — Small',
        'description' => "Creamy soft serve ice cream. Small size. $iceCreamFlavors",
        'price'       => 400,
        'image'       => 'brwnysundae.jpg',
        'category'    => 'Ice Cream',
        'size'        => 'Small',
    ],
    'softserve_md' => [
        'name'        => 'Soft Serve Ice Cream — Medium',
        'description' => "Creamy soft serve ice cream. Medium size. $iceCreamFlavors",
        'price'       => 500,
        'image'       => 'sundae.jpeg',
        'category'    => 'Ice Cream',
        'size'        => 'Medium',
    ],
    'softserve_lg' => [
        'name'        => 'Soft Serve Ice Cream — Large',
        'description' => "Creamy soft serve ice cream. Large size. $iceCreamFlavors",
        'price'       => 700,
        'image'       => 'cintstsun.png',
        'category'    => 'Ice Cream',
        'size'        => 'Large',
    ],

    // ══════════════════════════════════════════════════════════
    // HAND DIPPED ICE CREAM
    // Small $4.00 · Medium $5.00 · Large $7.00
    // ══════════════════════════════════════════════════════════
    'handdipped_sm' => [
        'name'        => 'Hand Dipped Ice Cream — Small',
        'description' => "Hand scooped ice cream. Small size. $iceCreamFlavors",
        'price'       => 400,
        'image'       => 'iceee.jpg',
        'category'    => 'Ice Cream',
        'size'        => 'Small',
    ],
    'handdipped_md' => [
        'name'        => 'Hand Dipped Ice Cream — Medium',
        'description' => "Hand scooped ice cream. Medium size. $iceCreamFlavors",
        'price'       => 500,
        'image'       => 'luckyice.png',
        'category'    => 'Ice Cream',
        'size'        => 'Medium',
    ],
    'handdipped_lg' => [
        'name'        => 'Hand Dipped Ice Cream — Large',
        'description' => "Hand scooped ice cream. Large size. $iceCreamFlavors",
        'price'       => 700,
        'image'       => 'cinbunice.png',
        'category'    => 'Ice Cream',
        'size'        => 'Large',
    ],

    // ══════════════════════════════════════════════════════════
    // CONES — 75¢ each
    // Kids Cone · Large Cone · Sugar Cone
    // Small Waffle Cone · Large Waffle Cone
    // ══════════════════════════════════════════════════════════
    'cone_kids' => [
        'name'        => 'Cone — Kids',
        'description' => 'Kids cone. Add to any ice cream order.',
        'price'       => 75,
        'image'       => 'sundae2.jpg',
        'category'    => 'Cones',
        'size'        => 'Kids',
    ],
    'cone_large' => [
        'name'        => 'Cone — Large',
        'description' => 'Large cone. Add to any ice cream order.',
        'price'       => 75,
        'image'       => 'cinbuncone.png',
        'category'    => 'Cones',
        'size'        => 'Large',
    ],
    'cone_sugar' => [
        'name'        => 'Sugar Cone',
        'description' => 'Classic sugar cone. Add to any ice cream order.',
        'price'       => 75,
        'image'       => 'sundae2.jpg',
        'category'    => 'Cones',
        'size'        => 'Regular',
    ],
    'cone_waffle_sm' => [
        'name'        => 'Small Waffle Cone',
        'description' => 'Small fresh-baked waffle cone. Add to any ice cream order.',
        'price'       => 75,
        'image'       => 'sundae2.jpg',
        'category'    => 'Cones',
        'size'        => 'Small',
    ],
    'cone_waffle_lg' => [
        'name'        => 'Large Waffle Cone',
        'description' => 'Large fresh-baked waffle cone. Add to any ice cream order.',
        'price'       => 75,
        'image'       => 'cinbuncone.png',
        'category'    => 'Cones',
        'size'        => 'Large',
    ],

    // ══════════════════════════════════════════════════════════
    // THE ICEBOX FROZEN CUP — $5.00
    // ══════════════════════════════════════════════════════════
    'frozen_cup' => [
        'name'        => 'The IceBox Frozen Cup',
        'description' => "Vanilla ice cream blended with your choice of toppings. $5.00. $iceCreamFlavors",
        'price'       => 500,
        'image'       => 'sundae2.jpg',
        'category'    => 'Ice Cream',
        'size'        => 'One Size',
    ],

    // ══════════════════════════════════════════════════════════
    // SIGNATURE SUNDAES
    // ══════════════════════════════════════════════════════════
    'sundae_brownie' => [
        'name'        => 'Brownie Sundae',
        'description' => 'Vanilla ice cream with hot fudge or caramel, whipped cream, topped with a cherry. $7.00',
        'price'       => 700,
        'image'       => 'brwnysundae.jpg',
        'category'    => 'Signature Sundaes',
        'size'        => 'One Size',
    ],
    'sundae_cookie' => [
        'name'        => 'Cookie Sundae',
        'description' => 'Vanilla ice cream drizzled with caramel and whipped cream. $6.00',
        'price'       => 600,
        'image'       => 'cookiemns.png',
        'category'    => 'Signature Sundaes',
        'size'        => 'One Size',
    ],
    'sundae_cinnamontoast' => [
        'name'        => 'Cinnamon Toast Donut Sundae',
        'description' => 'Vanilla ice cream topped with Cinnamon Toast Crunch, drizzled with caramel. $7.00',
        'price'       => 700,
        'image'       => 'cintstsun.png',
        'category'    => 'Signature Sundaes',
        'size'        => 'One Size',
    ],
    'sundae_fruitytooty' => [
        'name'        => 'Fruity Tooty Donut Sundae',
        'description' => 'Vanilla ice cream topped with Fruit Loops, drizzled with strawberry syrup. $7.00',
        'price'       => 700,
        'image'       => 'sundae.jpeg',
        'category'    => 'Signature Sundaes',
        'size'        => 'One Size',
    ],
    'sundae_donut' => [
        'name'        => 'Ice Cream Donut Sundae',
        'description' => 'A fresh donut topped with scoops of ice cream and your choice of toppings. $7.00',
        'price'       => 700,
        'image'       => 'oreosun.png',
        'category'    => 'Signature Sundaes',
        'size'        => 'One Size',
    ],

    // ══════════════════════════════════════════════════════════
    // UNIQUE DESSERTS
    // ══════════════════════════════════════════════════════════
    'dessert_waffle' => [
        'name'        => 'Create Your Own Waffle',
        'description' => 'Fresh waffle loaded with three scoops of ice cream, whipped cream, chocolate drizzle & your choice of toppings.',
        'price'       => 1100,
        'image'       => 'wafflesun.png',
        'category'    => 'Unique Desserts',
        'size'        => 'One Size',
    ],
    'dessert_friedoreo' => [
        'name'        => 'Fried Oreo Sundae',
        'description' => 'Fried Oreos dusted with powdered sugar, topped with vanilla ice cream, whipped cream & chocolate drizzle.',
        'price'       => 1000,
        'image'       => 'oreosun.png',
        'category'    => 'Unique Desserts',
        'size'        => 'One Size',
    ],
    'dessert_crepe' => [
        'name'        => 'Ice Cream Crepe',
        'description' => 'Crispy crepe filled with three scoops of ice cream, strawberry drizzle, whipped cream & rainbow sprinkles.',
        'price'       => 1050,
        'image'       => 'icecrepe.jpg',
        'category'    => 'Unique Desserts',
        'size'        => 'One Size',
    ],

    // ══════════════════════════════════════════════════════════
    // MILKSHAKES
    // Small $5.00 · Large $8.00
    // Flavors: Chocolate · Vanilla · Strawberry · Oreo
    // ══════════════════════════════════════════════════════════
    'milkshake_sm' => [
        'name'        => 'Milkshake — Small',
        'description' => "Hand-spun milkshake. Small size. $milkshakeFlavors",
        'price'       => 500,
        'image'       => 'oreomilkshake.png',
        'category'    => 'Milkshakes',
        'size'        => 'Small',
    ],
    'milkshake_lg' => [
        'name'        => 'Milkshake — Large',
        'description' => "Hand-spun milkshake. Large size. $milkshakeFlavors",
        'price'       => 800,
        'image'       => 'oreomilkshake.png',
        'category'    => 'Milkshakes',
        'size'        => 'Large',
    ],

    // ══════════════════════════════════════════════════════════
    // ICEBOX SNACKS
    // ══════════════════════════════════════════════════════════
    'snack_hotdog' => [
        'name'        => 'Hot Dog',
        'description' => 'Classic hot dog. $2.50',
        'price'       => 250,
        'image'       => 'fries.jpeg',
        'category'    => 'Snacks',
        'size'        => 'One Size',
    ],
    'snack_hotsausage' => [
        'name'        => 'Hot Sausage',
        'description' => 'Juicy hot sausage sandwich. $3.50',
        'price'       => 350,
        'image'       => 'fries.jpeg',
        'category'    => 'Snacks',
        'size'        => 'One Size',
    ],
    'snack_funnel' => [
        'name'        => 'Funnel Cake Fries',
        'description' => 'Crispy golden funnel cake fries dusted with powdered sugar. $6.00',
        'price'       => 600,
        'image'       => 'fries.jpeg',
        'category'    => 'Snacks',
        'size'        => 'One Size',
    ],

    // ══════════════════════════════════════════════════════════
    // PRETZELS
    // ══════════════════════════════════════════════════════════
    'pretzel_plain' => [
        'name'        => 'Plain Pretzel',
        'description' => 'Classic soft baked plain pretzel. $1.50',
        'price'       => 150,
        'image'       => 'pizzapretzel.png',
        'category'    => 'Pretzels',
        'size'        => 'One Size',
    ],
    'pretzel_cheese' => [
        'name'        => 'Cheese Pretzel',
        'description' => 'Soft pretzel topped with melted cheese. $2.00',
        'price'       => 200,
        'image'       => 'pizzapretzel.png',
        'category'    => 'Pretzels',
        'size'        => 'One Size',
    ],
    'pretzel_pizza' => [
        'name'        => 'Pizza Pretzel',
        'description' => 'Soft pretzel loaded with marinara sauce and mozzarella. $3.50',
        'price'       => 350,
        'image'       => 'pizzapretzel.png',
        'category'    => 'Pretzels',
        'size'        => 'One Size',
    ],
    'pretzel_pepperoni' => [
        'name'        => 'Beef Pepperoni Pizza',
        'description' => 'Toasted bread with marinara, melted mozzarella & beef pepperoni. $4.00',
        'price'       => 400,
        'image'       => 'pizzapretzel.png',
        'category'    => 'Pretzels',
        'size'        => 'One Size',
    ],

    // ══════════════════════════════════════════════════════════
    // TOPPINGS — 75¢ each
    // ══════════════════════════════════════════════════════════
    'topping_rainbow'     => ['name' => 'Rainbow Sprinkles',     'description' => 'Add rainbow sprinkles. 75¢',           'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_chocsprink'  => ['name' => 'Chocolate Sprinkles',   'description' => 'Add chocolate sprinkles. 75¢',         'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_chocchips'   => ['name' => 'Chocolate Chips',       'description' => 'Add chocolate chips. 75¢',             'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_oreos'       => ['name' => 'Oreos',                 'description' => 'Add crushed Oreos. 75¢',               'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_reesecups'   => ['name' => 'Reese Cups',            'description' => 'Add Reese Cups. 75¢',                  'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_brownie'     => ['name' => 'Brownie Bites',         'description' => 'Add brownie bites. 75¢',               'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_gummies'     => ['name' => 'Gummy Bears',           'description' => 'Add gummy bears. 75¢',                 'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_sourworm'    => ['name' => 'Sour Worm',             'description' => 'Add sour worms. 75¢',                  'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_lemonheads'  => ['name' => 'Lemon Heads',           'description' => 'Add lemon heads. 75¢',                 'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_walnuts'     => ['name' => 'Crushed Walnuts',       'description' => 'Add crushed walnuts. 75¢',             'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_cinntoast'   => ['name' => 'Cinnamon Toast Crunch', 'description' => 'Add Cinnamon Toast Crunch. 75¢',       'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_fruitloops'  => ['name' => 'Fruit Loops',           'description' => 'Add Fruit Loops. 75¢',                 'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_strawsyrup'  => ['name' => 'Strawberry Syrup',      'description' => 'Add strawberry syrup drizzle. 75¢',    'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_chocsyrup'   => ['name' => 'Chocolate Syrup',       'description' => 'Add chocolate syrup drizzle. 75¢',     'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_caramel'     => ['name' => 'Caramel Syrup',         'description' => 'Add caramel syrup drizzle. 75¢',       'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
    'topping_cherries'    => ['name' => 'Cherries',              'description' => 'Add cherries on top. 75¢',             'price' => 75,  'image' => 'sundae2.jpg', 'category' => 'Toppings', 'size' => 'Add-on'],
];

// ─── Cart Helpers ─────────────────────────────────────────────
function getCart(): array {
    return $_SESSION['cart'] ?? [];
}

function addToCart(string $productId, int $qty = 1): void {
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $qty;
}

function removeFromCart(string $productId): void {
    unset($_SESSION['cart'][$productId]);
}

function updateCartQty(string $productId, int $qty): void {
    if ($qty <= 0) {
        removeFromCart($productId);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cartTotal(array $cart, array $products): int {
    $total = 0;
    foreach ($cart as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id]['price'] * $qty;
        }
    }
    return $total;
}

function formatPrice(int $cents): string {
    return '$' . number_format($cents / 100, 2);
}

// ─── Action Handling ──────────────────────────────────────────
$action  = $_POST['action']  ?? $_GET['action']  ?? '';
$message = '';
$error   = '';

// Add to cart
if ($action === 'add' && isset($_POST['product_id'])) {
    $pid = htmlspecialchars($_POST['product_id']);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if (isset($products[$pid])) {
        addToCart($pid, $qty);
        $message = htmlspecialchars($products[$pid]['name']) . ' added to cart!';
    }
}

// Update quantity
if ($action === 'update' && isset($_POST['product_id'], $_POST['qty'])) {
    $pid = htmlspecialchars($_POST['product_id']);
    updateCartQty($pid, (int)$_POST['qty']);
}

// Remove item
if ($action === 'remove' && isset($_POST['product_id'])) {
    $pid = htmlspecialchars($_POST['product_id']);
    removeFromCart($pid);
}

// Clear cart
if ($action === 'clear') {
    $_SESSION['cart'] = [];
}

// ─── Stripe: Create Payment Intent ────────────────────────────
// Called via fetch() from the checkout page JS
if ($action === 'create_payment_intent') {
    header('Content-Type: application/json');
    $cart  = getCart();
    $total = cartTotal($cart, $products);

    if ($total <= 0) {
        echo json_encode(['error' => 'Your cart is empty.']);
        exit;
    }

    try {
        // Build line items description for metadata
        $lineItems = [];
        foreach ($cart as $id => $qty) {
            if (isset($products[$id])) {
                $lineItems[] = $products[$id]['name'] . ' x' . $qty;
            }
        }

        $intent = \Stripe\PaymentIntent::create([
            'amount'   => $total,
            'currency' => 'usd',

            // Enable Apple Pay, Google Pay, debit cards, credit cards
            'payment_method_types' => ['card'],

            // automatic_payment_methods enables Apple Pay & Google Pay
            // automatically when available on the device
            'automatic_payment_methods' => ['enabled' => true],

            'metadata' => [
                'order_items' => implode(', ', $lineItems),
                'item_count'  => count($cart),
            ],

            'description' => 'IceBox Frozen Treats Order',
        ]);

        // Store intent ID in session so we can confirm later
        $_SESSION['payment_intent_id'] = $intent->id;

        echo json_encode([
            'clientSecret'    => $intent->client_secret,
            'publishableKey'  => $stripePublishableKey,
            'total'           => $total,
            'totalFormatted'  => formatPrice($total),
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ─── Stripe: Webhook Handler ──────────────────────────────────
// Point your Stripe webhook to: https://yourdomain.com/checkout.php?action=webhook
// Events to enable in Stripe Dashboard: payment_intent.succeeded, payment_intent.payment_failed
if ($action === 'webhook') {
    $payload   = file_get_contents('php://input');
    $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            $stripeWebhookSecret
        );
    } catch (\UnexpectedValueException $e) {
        http_response_code(400);
        exit('Invalid payload');
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        http_response_code(400);
        exit('Invalid signature');
    }

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.succeeded':
            $intent = $event->data->object;
            // TODO: Mark order as paid in your database
            // TODO: Send confirmation email to customer
            // TODO: Trigger fulfillment / notify kitchen
            error_log("Payment succeeded: " . $intent->id);
            break;

        case 'payment_intent.payment_failed':
            $intent = $event->data->object;
            // TODO: Notify customer of failure
            error_log("Payment failed: " . $intent->id);
            break;

        default:
            // Unexpected event type — log and ignore
            error_log("Unhandled Stripe event: " . $event->type);
    }

    http_response_code(200);
    echo json_encode(['received' => true]);
    exit;
}

// ─── Order Success Page ───────────────────────────────────────
$showSuccess = isset($_GET['success']) && $_GET['success'] === '1';
if ($showSuccess) {
    $_SESSION['cart'] = []; // Clear cart after successful payment
}

// ─── Page State ───────────────────────────────────────────────
$cart        = getCart();
$cartTotal   = cartTotal($cart, $products);
$cartCount   = array_sum($cart);
$view        = $_GET['view'] ?? 'shop'; // shop | cart | checkout
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Online — The IceBox Frozen Treats</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ranchers&display=swap" rel="stylesheet">

  <!-- Stripe.js — must load from Stripe's CDN, do not self-host -->
  <script src="https://js.stripe.com/v3/"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue-dark:  #0083B0;
      --blue-light: #00B4DB;
      --ice:        rgb(48, 194, 239);
      --wheat:      wheat;
      --card-bg:    wheat;
      --radius:     1em;
    }

    body {
      font-family: "Ranchers", system-ui;
      background: linear-gradient(to right, #0083B0, #00B4DB);
      min-height: 100vh;
      color: var(--ice);
    }

    /* ── Nav ── */
    .site-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      background: rgba(0,0,0,0.15);
      position: sticky;
      top: 0;
      z-index: 100;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .site-nav a {
      color: var(--ice);
      text-decoration: none;
      font-size: 1.1rem;
    }


    .nav-links { display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; }

    .cart-badge {
      background: white;
      color: var(--blue-dark);
      border-radius: 2em;
      padding: 0.3em 1em;
      font-size: 1rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4em;
    }

    /* ── Page wrapper ── */
    .page { max-width: 1200px; margin-inline: auto; padding: 2rem 1.5rem; }

    h1 { font-size: clamp(2rem, 5vw, 4rem); text-align: center; margin-bottom: 2rem; }
    h2 { font-size: clamp(1.4rem, 3vw, 2.5rem); margin-bottom: 1rem; }

    /* ── Flash messages ── */
    .flash {
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
      text-align: center;
    }
    .flash.success { background: rgba(255,255,255,0.25); color: white; }
    .flash.error   { background: rgba(220,50,50,0.3);   color: #ffe0e0; }

    /* ── Category tabs ── */
    .category-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      justify-content: center;
      margin-bottom: 1.5rem;
    }

    .cat-tab {
      font-family: "Ranchers", system-ui;
      font-size: 0.95rem;
      padding: 0.4em 1.1em;
      border-radius: 2em;
      background: rgba(255,255,255,0.15);
      color: white;
      text-decoration: none;
      border: 1px solid rgba(255,255,255,0.3);
      transition: background 0.2s;
      white-space: nowrap;
    }

    .cat-tab:hover  { background: rgba(255,255,255,0.28); color: white; }
    .cat-tab.active { background: white; color: var(--blue-dark); border-color: white; }

    /* ── Cart sticky banner ── */
    .cart-banner {
      background: rgba(0,0,0,0.25);
      color: white;
      text-align: center;
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-family: "Ranchers", system-ui;
      font-size: 1.05rem;
      position: sticky;
      top: 160px;
      z-index: 50;
    }

    /* ── Category section heading ── */
    .category-section { margin-bottom: 3rem; }

    .category-heading {
      font-family: "Ranchers", system-ui;
      font-size: 1.8rem;
      color: white;
      border-bottom: 2px solid rgba(255,255,255,0.3);
      padding-bottom: 0.4rem;
      margin-bottom: 1.5rem;
    }

    /* ── Size badge ── */
    .size-badge {
      display: inline-block;
      font-family: "Ranchers", system-ui;
      font-size: 0.75rem;
      background: var(--blue-dark);
      color: white;
      padding: 0.15em 0.7em;
      border-radius: 2em;
      margin-bottom: 0.4rem;
    }

    /* ── Product grid ── */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 2rem;
    }

    .product-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .product-card .card-body {
      padding: 1.2rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
      flex: 1;
    }

    .product-card h3 { font-size: 1.4rem; color: var(--blue-dark); }
    .product-card p  { font-size: 0.95rem; color: #555; font-family: system-ui; }

    .product-price {
      font-size: 1.5rem;
      color: var(--blue-dark);
      margin-top: auto;
    }

    /* ── Add-to-cart form ── */
    .add-form {
      display: flex;
      gap: 0.5rem;
      align-items: center;
      margin-top: 0.5rem;
    }

    .qty-input {
      width: 60px;
      padding: 0.4em 0.6em;
      border: 2px solid var(--blue-dark);
      border-radius: 0.5em;
      font-family: "Ranchers", system-ui;
      font-size: 1rem;
      color: var(--blue-dark);
      text-align: center;
    }

    /* ── Buttons ── */
    .btn {
      font-family: "Ranchers", system-ui;
      font-size: 1rem;
      border: none;
      border-radius: 0.6em;
      padding: 0.5em 1.2em;
      cursor: pointer;
      transition: opacity 0.15s, transform 0.1s;
      text-decoration: none;
      display: inline-block;
    }

    .btn:hover  { opacity: 0.88; }
    .btn:active { transform: scale(0.97); }

    .btn-primary   { background: var(--blue-dark); color: white; }
    .btn-secondary { background: white;            color: var(--blue-dark); }
    .btn-danger    { background: #c0392b;           color: white; }
    .btn-success   { background: #27ae60;           color: white; font-size: 1.2rem; padding: 0.7em 2em; width: 100%; text-align: center; }

    /* ── Cart table ── */
    .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }

    .cart-table th, .cart-table td {
      padding: 0.8rem 1rem;
      text-align: left;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .cart-table th { font-size: 1rem; opacity: 0.8; }
    .cart-table td { font-size: 1rem; }

    .cart-table .item-name { font-size: 1.1rem; }

    .cart-actions { display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap; }

    .cart-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(255,255,255,0.15);
      border-radius: var(--radius);
      padding: 1rem 1.5rem;
      margin-bottom: 1.5rem;
      font-size: 1.4rem;
    }

    /* ── Checkout form ── */
    .checkout-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    @media (max-width: 720px) {
      .checkout-layout { grid-template-columns: 1fr; }
    }

    .checkout-box {
      background: rgba(255,255,255,0.12);
      border-radius: var(--radius);
      padding: 1.5rem;
    }

    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-size: 0.95rem; margin-bottom: 0.3rem; opacity: 0.85; }

    .form-input {
      width: 100%;
      padding: 0.6em 0.8em;
      border: 2px solid rgba(255,255,255,0.4);
      border-radius: 0.5em;
      background: rgba(255,255,255,0.15);
      color: white;
      font-family: "Ranchers", system-ui;
      font-size: 1rem;
    }

    .form-input::placeholder { color: rgba(255,255,255,0.5); }
    .form-input:focus { outline: none; border-color: white; }

    /* ── Stripe Elements mount point ── */
    #payment-element {
      background: white;
      padding: 1rem;
      border-radius: 0.6em;
      min-height: 60px;
      margin-bottom: 1rem;
    }

    /* Apple Pay button (Stripe renders this inside Payment Element automatically) */
    #express-checkout-element {
      margin-bottom: 1rem;
    }

    #payment-message {
      color: #ffe0e0;
      font-size: 1rem;
      text-align: center;
      min-height: 1.5rem;
      margin-bottom: 0.5rem;
    }

    #submit-btn { position: relative; }
    #submit-btn .spinner { display: none; }
    #submit-btn.loading .spinner { display: inline-block; }
    #submit-btn.loading .btn-text { opacity: 0; }

    /* ── Order summary sidebar ── */
    .order-summary-item {
      display: flex;
      justify-content: space-between;
      padding: 0.4rem 0;
      border-bottom: 1px solid rgba(255,255,255,0.15);
      font-size: 1rem;
    }

    .order-total {
      display: flex;
      justify-content: space-between;
      font-size: 1.3rem;
      padding-top: 0.8rem;
      margin-top: 0.4rem;
    }

    /* ── Success page ── */
    .success-box {
      text-align: center;
      background: rgba(255,255,255,0.15);
      border-radius: var(--radius);
      padding: 3rem 2rem;
      max-width: 600px;
      margin: 3rem auto;
    }

    .success-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
      display: block;
    }

    /* ── Empty cart ── */
    .empty-cart {
      text-align: center;
      padding: 4rem 2rem;
      opacity: 0.7;
      font-size: 1.3rem;
    }

    /* ── Responsive tweaks ── */
    @media (max-width: 480px) {
      .site-nav { padding: 0.8rem 1rem; }
      .page { padding: 1.5rem 1rem; }
      .cart-table th:nth-child(2),
      .cart-table td:nth-child(2) { display: none; } /* hide unit price on very small screens */
    }
  </style>
</head>
<body>

<!-- ── Navigation ── -->
<nav class="site-nav">
  <a href="index.html" class="logo"><img src="iceboxbg.jpg" alt="logo" width="150" height="150">
</a>
  <div class="nav-links">
    <a href="checkout.php?view=shop">Menu</a>
    <a href="checkout.php?view=cart" class="cart-badge">
      🛒 Cart
      <?php if ($cartCount > 0): ?>
        <span>(<?= $cartCount ?>)</span>
      <?php endif; ?>
    </a>
  </div>
</nav>

<div class="page">

<?php if ($showSuccess): ?>
<!-- ════════════════════════════════════
     ORDER SUCCESS
     ════════════════════════════════════ -->
<div class="success-box">
  <span class="success-icon">✅</span>
  <h1>Order Placed!</h1>
  <p style="font-size:1.1rem; margin: 1rem 0 2rem; font-family: system-ui;">
    Thank you for your order! You'll receive a confirmation email shortly.
    Your frozen treats will be ready for pickup soon!
  </p>
  <a href="checkout.php?view=shop" class="btn btn-secondary">Order Again</a>
</div>

<?php elseif ($view === 'checkout'): ?>
<!-- ════════════════════════════════════
     CHECKOUT
     ════════════════════════════════════ -->
<h1>Checkout</h1>

<?php if (empty($cart)): ?>
  <div class="empty-cart">
    Your cart is empty. <a href="checkout.php?view=shop" style="color:white;">Add some treats!</a>
  </div>
<?php else: ?>

<div class="checkout-layout">

  <!-- Left: Customer info + payment -->
  <div>
    <div class="checkout-box" style="margin-bottom: 1.5rem;">
      <h2>Your Info</h2>
      <div class="form-group">
        <label>Full name</label>
        <input type="text" class="form-input" id="customer-name" placeholder="Jane Smith" autocomplete="name">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-input" id="customer-email" placeholder="jane@example.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label>Phone (optional)</label>
        <input type="tel" class="form-input" id="customer-phone" placeholder="(215) 555-0100" autocomplete="tel">
      </div>
      <div class="form-group">
        <label>Flavor &amp; Size Notes <span style="opacity:0.7; font-size:0.85rem;">(e.g. "Water Ice — Cherry, Gelati — Blue Raspberry + Vanilla")</span></label>
        <textarea class="form-input" id="order-notes" rows="3" placeholder="List your flavor and size choices here for each item..." style="resize:vertical;"></textarea>
      </div>
    </div>

    <div class="checkout-box">
      <h2>Payment</h2>
      <p style="font-size:0.9rem; opacity:0.8; margin-bottom:1rem; font-family:system-ui;">
        Accepts Apple Pay, Google Pay, debit &amp; credit cards. All payments secured by Stripe.
      </p>

      <!-- Express checkout (Apple Pay / Google Pay) renders here automatically -->
      <div id="express-checkout-element"></div>

      <div id="payment-message"></div>

      <!-- Card / debit card fields (Stripe Payment Element) -->
      <div id="payment-element"></div>

      <button id="submit-btn" class="btn btn-success" onclick="handleSubmit(event)">
        <span class="btn-text">Pay <?= formatPrice($cartTotal) ?></span>
        <span class="spinner"> ⏳ Processing…</span>
      </button>
    </div>
  </div>

  <!-- Right: Order summary -->
  <div class="checkout-box" style="align-self: start;">
    <h2>Order Summary</h2>
    <?php foreach ($cart as $id => $qty): ?>
      <?php if (isset($products[$id])): $p = $products[$id]; ?>
      <div class="order-summary-item">
        <span><?= htmlspecialchars($p['name']) ?> × <?= (int)$qty ?></span>
        <span><?= formatPrice($p['price'] * $qty) ?></span>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
    <div class="order-total">
      <span>Total</span>
      <strong><?= formatPrice($cartTotal) ?></strong>
    </div>
    <a href="checkout.php?view=cart" class="btn btn-secondary" style="margin-top:1rem; display:block; text-align:center;">
      ← Edit cart
    </a>
  </div>

</div>

<script>
// ── Stripe Checkout JS ─────────────────────────────────────────
let stripe, elements;

async function initStripe() {
  // 1. Ask PHP to create a PaymentIntent and give us the clientSecret
  const res = await fetch('checkout.php?action=create_payment_intent', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
  });

  const data = await res.json();

  if (data.error) {
    document.getElementById('payment-message').textContent = data.error;
    return;
  }

  // 2. Initialize Stripe with publishable key
  stripe = Stripe(data.publishableKey);

  // 3. Create Elements instance — this enables Apple Pay, Google Pay,
  //    debit cards, and credit cards all from one setup
  elements = stripe.elements({
    clientSecret: data.clientSecret,
    appearance: {
      theme: 'stripe',
      variables: {
        colorPrimary: '#0083B0',
        fontFamily: '"Ranchers", system-ui',
        borderRadius: '8px',
      },
    },
  });

  // 4. Mount the Express Checkout Element (Apple Pay / Google Pay)
  //    Stripe automatically shows this button only on supported devices/browsers
  const expressElement = elements.create('expressCheckout');
  expressElement.mount('#express-checkout-element');

  expressElement.on('confirm', async (event) => {
    const name  = document.getElementById('customer-name').value;
    const email = document.getElementById('customer-email').value;

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: window.location.origin + '/checkout.php?success=1',
        payment_method_data: {
          billing_details: { name, email }
        },
      },
    });

    if (error) {
      document.getElementById('payment-message').textContent = error.message;
    }
  });

  // 5. Mount the Payment Element (card / debit card input fields)
  const paymentElement = elements.create('payment', {
    layout: { type: 'tabs' },
  });
  paymentElement.mount('#payment-element');
}

async function handleSubmit(e) {
  e.preventDefault();

  const btn = document.getElementById('submit-btn');
  const msg = document.getElementById('payment-message');
  btn.classList.add('loading');
  btn.disabled = true;
  msg.textContent = '';

  const name  = document.getElementById('customer-name').value.trim();
  const email = document.getElementById('customer-email').value.trim();
  const notes = document.getElementById('order-notes')?.value.trim() || '';

  if (!name || !email) {
    msg.textContent = 'Please fill in your name and email.';
    btn.classList.remove('loading');
    btn.disabled = false;
    return;
  }

  // Confirm the payment — Stripe handles 3D Secure, bank redirects, etc.
  const { error } = await stripe.confirmPayment({
    elements,
    confirmParams: {
      return_url: window.location.origin + '/checkout.php?success=1',
      payment_method_data: {
        billing_details: { name, email }
      },
    },
  });

  // If we get here, something went wrong (successful payments redirect away)
  if (error) {
    msg.textContent = error.message;
    btn.classList.remove('loading');
    btn.disabled = false;
  }
}

// Initialize when page loads
initStripe();
</script>

<?php endif; // end checkout with items ?>

<?php elseif ($view === 'cart'): ?>
<!-- ════════════════════════════════════
     CART
     ════════════════════════════════════ -->
<h1>Your Cart</h1>

<?php if ($message): ?>
  <div class="flash success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (empty($cart)): ?>
  <div class="empty-cart">
    Your cart is empty! <a href="checkout.php?view=shop" style="color:white;">Browse the menu →</a>
  </div>
<?php else: ?>

<table class="cart-table">
  <thead>
    <tr>
      <th>Item</th>
      <th>Unit price</th>
      <th>Qty</th>
      <th>Subtotal</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($cart as $id => $qty): ?>
      <?php if (!isset($products[$id])) continue; $p = $products[$id]; ?>
      <tr>
        <td class="item-name"><?= htmlspecialchars($p['name']) ?></td>
        <td><?= formatPrice($p['price']) ?></td>
        <td>
          <form method="POST" action="checkout.php?view=cart" style="display:inline-flex; gap:0.3rem; align-items:center;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
            <input type="number" name="qty" value="<?= (int)$qty ?>" min="0" max="99" class="qty-input">
            <button type="submit" class="btn btn-secondary" style="padding:0.3em 0.7em; font-size:0.85rem;">Update</button>
          </form>
        </td>
        <td><?= formatPrice($p['price'] * $qty) ?></td>
        <td>
          <form method="POST" action="checkout.php?view=cart" style="display:inline;">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
            <button type="submit" class="btn btn-danger" style="padding:0.3em 0.7em; font-size:0.85rem;">✕</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="cart-total-row">
  <span>Total</span>
  <strong><?= formatPrice($cartTotal) ?></strong>
</div>

<div class="cart-actions">
  <form method="POST" action="checkout.php?view=shop">
    <input type="hidden" name="action" value="clear">
    <button type="submit" class="btn btn-danger">Clear cart</button>
  </form>
  <a href="checkout.php?view=shop" class="btn btn-secondary">← Keep shopping</a>
  <a href="checkout.php?view=checkout" class="btn btn-primary">Proceed to checkout →</a>
</div>

<?php endif; ?>

<?php else: ?>
<!-- ════════════════════════════════════
     SHOP / MENU
     ════════════════════════════════════ -->
<h1>Order Online</h1>
<p style="text-align:center; font-family:'Ranchers',system-ui; color:rgba(255,255,255,0.8); margin-bottom:1.5rem; font-size:1rem;">
  Note your flavor &amp; size in the Order Notes at checkout.
</p>

<?php if ($message): ?>
  <div class="flash success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php
$categories    = ['All'];
$activeCategory = $_GET['cat'] ?? 'All';
foreach ($products as $p) {
    if (!in_array($p['category'], $categories)) {
        $categories[] = $p['category'];
    }
}
?>

<!-- Category tabs -->
<div class="category-tabs">
  <?php foreach ($categories as $cat): ?>
    <a href="checkout.php?view=shop&cat=<?= urlencode($cat) ?>"
       class="cat-tab <?= $activeCategory === $cat ? 'active' : '' ?>">
      <?= htmlspecialchars($cat) ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- Sticky cart banner -->
<?php if ($cartCount > 0): ?>
<div class="cart-banner">
  🛒 <?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?> in cart — <?= formatPrice($cartTotal) ?>
  &nbsp;·&nbsp;
  <a href="checkout.php?view=cart" style="color:white; text-decoration:underline;">View Cart →</a>
</div>
<?php endif; ?>

<?php
// Group by category
$grouped = [];
foreach ($products as $id => $p) {
    $grouped[$p['category']][$id] = $p;
}
?>

<?php foreach ($grouped as $catName => $items): ?>
  <?php if ($activeCategory !== 'All' && $activeCategory !== $catName) continue; ?>
  <div class="category-section">
    <h2 class="category-heading"><?= htmlspecialchars($catName) ?></h2>
    <div class="product-grid">
      <?php foreach ($items as $id => $p): ?>
      <div class="product-card">
        <img src="<?= htmlspecialchars($p['image']) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>"
             onerror="this.src='https://placehold.co/400x180/00B4DB/white?text=IceBox'">
        <div class="card-body">
          <!-- Size badge -->
          <?php if (!empty($p['size']) && $p['size'] !== 'One Size' && $p['size'] !== 'Add-on'): ?>
          <span class="size-badge"><?= htmlspecialchars($p['size']) ?></span>
          <?php endif; ?>

          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p><?= htmlspecialchars($p['description']) ?></p>
          <div class="product-price"><?= formatPrice($p['price']) ?></div>

          <form method="POST" action="checkout.php?view=shop&cat=<?= urlencode($activeCategory) ?>" class="add-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
            <input type="number" name="qty" value="1" min="1" max="99" class="qty-input">
            <button type="submit" class="btn btn-primary">Add to cart</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php if ($cartCount > 0): ?>
<div style="text-align:center; margin-top:3rem;">
  <a href="checkout.php?view=cart" class="btn btn-success" style="display:inline-block; width:auto;">
    View cart (<?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?>) — <?= formatPrice($cartTotal) ?> →
  </a>
</div>
<?php endif; ?>

<?php endif; // end view switch ?>

</div><!-- /.page -->
</body>
</html>
