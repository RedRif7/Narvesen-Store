<?php
class Store {
    private $storeProducts;
    private $cart;
    private $customerBalance;

    public function __construct($filename, $balance) {
        $this->storeProducts = json_decode(file_get_contents($filename), true);
        $this->cart = [];
        $this->customerBalance = $balance;
    }

    public function displayProducts() {
        foreach ($this->storeProducts as $id => $product) {
            echo "ID: $id, Name: {$product['name']}, Price: {$product['price']}, Quantity: {$product['quantity']}\n";
        }
    }

    public function addToCart($id, $quantity) {
        if (!isset($this->storeProducts[$id])) {
            echo "Invalid product ID.\n";
            return;
        }
        if ($quantity <= 0) {
            echo "Quantity must be greater than 0.\n";
            return;
        }
        if ($quantity > $this->storeProducts[$id]['quantity']) {
            echo "Not enough quantity in stock.\n";
            return;
        }
        if (!isset($this->cart[$id])) {
            $this->cart[$id] = 0;
        }
        $this->cart[$id] += $quantity;
        $this->storeProducts[$id]['quantity'] -= $quantity;
    }

    public function displayCart() {
        $total = 0;
        foreach ($this->cart as $id => $quantity) {
            $product = $this->storeProducts[$id];
            $cost = $product['price'] * $quantity;
            echo "ID: $id, Name: {$product['name']}, Quantity: $quantity, Total Cost: $cost\n";
            $total += $cost;
        }
        echo "Total: $total\n";
    }

    public function purchaseItems() {
        $total = array_sum(array_map(function($id, $quantity) {
            return $this->storeProducts[$id]['price'] * $quantity;
        }, array_keys($this->cart), $this->cart));

        if ($total > $this->customerBalance) {
            echo "Insufficient balance.\n";
            return;
        }

        $this->customerBalance -= $total;
        $this->cart = [];
        echo "Purchase successful! Remaining balance: {$this->customerBalance}\n";
    }
}

$storeFile = new Store('products.json', 1000);
while (true) {
    echo "1. Display products\n2. Add to cart\n3. Display cart\n4. Purchase\n5. Exit\n";
    $customerChoice = readline("Enter your choice: ");
    switch ($customerChoice) {
        case 1:
            $storeFile->displayProducts();
            break;
        case 2:
            $id = readline("Enter product ID: ");
            $quantity = readline("Enter quantity: ");
            $storeFile->addToCart($id, $quantity);
            break;
        case 3:
            $storeFile->displayCart();
            break;
        case 4:
            $storeFile->purchaseItems();
            break;
        case 5:
            exit();
        default:
            echo "Invalid choice.\n";
    }
}
?>
