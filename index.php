<?php

$products = [
    [
        "name" => "Paper Squishy",
        "price" => 8000
    ],
    [
        "name" => "Sponge Squishy",
        "price" => 10000
    ],
    [
        "name" => "Flanel Keychain",
        "price" => 12000
    ],
    [
        "name" => "Painting by Numbers Kit",
        "price" => 35000
    ],
    [
        "name" => "Sticker",
        "price" => 3000
    ],
    [
        "name" => "Pencil",
        "price" => 2500
    ]
];

$customerName = "";
$isMember = false;
$voucherCode = "";
$selectedItems = [];

$errors = [];

$subtotal = 0;
$memberDiscount = 0;
$voucherDiscount = 0;
$packagingFee = 0;
$total = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customerName = trim($_POST["customer_name"] ?? "");
    $isMember = isset($_POST["is_member"]);
    $voucherCode = trim($_POST["voucher_code"] ?? "");

    $quantities = $_POST["quantity"] ?? [];

    // VALIDATION CUSTOMER
    if ($customerName === "") {
        $errors[] = "Customer name harus diisi";
    }

    // CARI PRODUK YANG DIPILIH
    foreach ($products as $index => $product) {

        $quantity = (int)($quantities[$index] ?? 0);

        if ($quantity > 0) {

            $itemSubtotal = $product["price"] * $quantity;

            $selectedItems[] = [
                "name" => $product["name"],
                "price" => $product["price"],
                "quantity" => $quantity,
                "subtotal" => $itemSubtotal
            ];

            $subtotal += $itemSubtotal;
        }
    }

    // VALIDATION PRODUCT
    if (count($selectedItems) === 0) {
        $errors[] = "Minimal pilih satu produk";
    }

    // CALCULATION
    if (count($errors) === 0) {

        // MEMBER DISCOUNT 10%
        if ($isMember === true) {
            $memberDiscount = $subtotal * 0.10;
        }

        // VOUCHER
        if ($voucherCode === "CRAFT2026" && $subtotal >= 75000) {

            $voucherDiscount = 10000;

        } elseif ($voucherCode !== "") {

            $errors[] = "Voucher tidak valid atau minimum subtotal belum terpenuhi";
        }

        // PACKAGING
        $packagingFee = 2000;

        // TOTAL
        $total =
            $subtotal
            - $memberDiscount
            - $voucherDiscount
            + $packagingFee;
    }
}

function formatRupiah($number)
{
    return "Rp" . number_format($number, 0, ",", ".");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Kiddy Craftshop</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body>

<main class="page-wrapper">

    <!-- HERO -->

    <section class="hero-card">

        <p class="eyebrow">
            PHP Logic Practice
        </p>

        <h1>
            Kiddy Craftshop
        </h1>

        <p class="subtitle">
            Simple checkout system for handmade crafts,
            cute items, and stationery.
        </p>

    </section>


    <!-- PRODUCT + ORDER FORM -->

    <section class="content-grid">

        <div class="card">

            <h2>
                Available Products
            </h2>

            <p class="hint">
                Choose the products and enter the quantity.
            </p>

            <form method="POST">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Quantity
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($products as $index => $product) { ?>

                        <tr>

                            <td>
                                <?php echo $product["name"]; ?>
                            </td>

                            <td>
                                <?php echo formatRupiah($product["price"]); ?>
                            </td>

                            <td>

                                <input
                                    type="number"
                                    name="quantity[<?php echo $index; ?>]"
                                    min="0"
                                    value="<?php echo $_POST["quantity"][$index] ?? 0; ?>"
                                >

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

        </div>


        <div class="card">

            <h2>
                Order Form
            </h2>

            <p class="hint">
                Enter customer information before processing the order.
            </p>

            <div class="info-list">

                <div>

                    <span>
                        Customer Name
                    </span>

                    <input
                        type="text"
                        name="customer_name"
                        value="<?php echo htmlspecialchars($customerName); ?>"
                        placeholder="Nama customer"
                    >

                </div>


                <div>

                    <span>
                        Member
                    </span>

                    <label>

                        <input
                            type="checkbox"
                            name="is_member"
                            <?php echo $isMember ? "checked" : ""; ?>
                        >

                        Yes, I am a member

                    </label>

                </div>


                <div>

                    <span>
                        Voucher
                    </span>

                    <input
                        type="text"
                        name="voucher_code"
                        value="<?php echo htmlspecialchars($voucherCode); ?>"
                        placeholder="CRAFT2026"
                    >

                </div>

            </div>


            <button type="submit">
                Process Order
            </button>

        </div>

        </form>

    </section>


    <!-- VALIDATION -->

    <section class="card">

        <h2>
            Validation Result
        </h2>

        <p class="hint">
            Display error messages if the order data is not valid.
        </p>


        <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>

            <?php if (count($errors) > 0) { ?>

                <div class="notice error">

                    <strong>
                        Silahkan perbaiki error dibawah ini
                    </strong>

                    <ul>

                        <?php foreach ($errors as $error) { ?>

                            <li>
                                <?php echo $error; ?>
                            </li>

                        <?php } ?>

                    </ul>

                </div>

            <?php } else { ?>

                <div class="notice success">

                    Order data valid.

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="notice warning">

                Silahkan isi form untuk memproses order.

            </div>

        <?php } ?>

    </section>


    <!-- INVOICE -->

    <section class="card invoice-card">

        <h2>
            Invoice
        </h2>

        <p class="hint">
            Invoice will appear after a valid order is processed.
        </p>


        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && count($errors) === 0) { ?>

            <div class="invoice-row">

                <span>
                    Customer
                </span>

                <strong>
                    <?php echo htmlspecialchars($customerName); ?>
                </strong>

            </div>


            <h3>
                Items
            </h3>


            <?php foreach ($selectedItems as $item) { ?>

                <div class="invoice-row">

                    <span>

                        <?php echo $item["name"]; ?>

                        ×

                        <?php echo $item["quantity"]; ?>

                    </span>

                    <strong>

                        <?php echo formatRupiah($item["subtotal"]); ?>

                    </strong>

                </div>

            <?php } ?>


            <div class="invoice-row">

                <span>
                    Subtotal
                </span>

                <strong>
                    <?php echo formatRupiah($subtotal); ?>
                </strong>

            </div>


            <div class="invoice-row">

                <span>
                    Member Discount
                </span>

                <strong>
                    <?php echo formatRupiah($memberDiscount); ?>
                </strong>

            </div>


            <div class="invoice-row">

                <span>
                    Voucher Discount
                </span>

                <strong>
                    <?php echo formatRupiah($voucherDiscount); ?>
                </strong>

            </div>


            <div class="invoice-row">

                <span>
                    Packaging Fee
                </span>

                <strong>
                    <?php echo formatRupiah($packagingFee); ?>
                </strong>

            </div>


            <div class="invoice-row total-row">

                <span>
                    Total
                </span>

                <strong>
                    <?php echo formatRupiah($total); ?>
                </strong>

            </div>


        <?php } else { ?>

            <div class="empty-state large">

                Invoice cannot be generated because
                the order data is not valid.

            </div>

        <?php } ?>

    </section>