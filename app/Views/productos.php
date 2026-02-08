<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Brixo - Portafolio</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom styles -->
    <link href="/css/brixo.css" rel="stylesheet">
    <link href="pricing.css" rel="stylesheet">
    <style>
        /* Fix for pricing.css if it relies on BS4 */
        .pricing-header {
            max-width: 700px;
        }
    </style>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4">Portafolio de Servicios</h1>
            <p class="lead">¿Necesitas profesionales fiables para obra, construcción, plomería, electricidad, domótica,
                carpintería y más? En Brixo conectamos tu proyecto con técnicos verificados, presupuestos claros y
                garantía de calidad. Publica tu trabajo y recibe ofertas competitivas en menos de 24 horas.</p>
            <p>
                <a class="btn btn-lg btn-primary" href="/crear-proyecto" role="button">Publica tu proyecto gratis</a>
                <a class="btn btn-lg btn-outline-primary" href="/como-funciona" role="button">Cómo funciona</a>
            </p>
        </div>

        <div class="px-4 pt-5 my-5 text-center border-bottom">
            <h1 class="display-4 fw-bold text-body-emphasis">Centered screenshot</h1>
            <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">Quickly design and customize responsive mobile-first sites with Bootstrap, the
                    world’s most popular front-end open source toolkit, featuring Sass variables and mixins, responsive
                    grid system, extensive prebuilt components, and powerful JavaScript plugins.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mb-5"> <button type="button"
                        class="btn btn-primary btn-lg px-4 me-sm-3">Ver contratos</button> <button type="button"
                        class="btn btn-outline-secondary btn-lg px-4">Ver profesionales</button> </div>
            </div>
            <div class="overflow-hidden" style="max-height: 30vh;">
                <div class="container px-5"> <img src="bootstrap-docs.png"
                        class="img-fluid border rounded-3 shadow-lg mb-4" alt="Example image" width="700" height="500"
                        loading="lazy"> </div>
            </div>
        </div>

        <div class="container">
            <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Free</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">$0 <small class="text-muted fw-light">/ mo</small>
                            </h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>10 users included</li>
                                <li>2 GB of storage</li>
                                <li>Email support</li>
                                <li>Help center access</li>
                            </ul>
                            <button type="button" class="w-100 btn btn-lg btn-outline-primary">Sign up for free</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Pro</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">$15 <small class="text-muted fw-light">/
                                    mo</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>20 users included</li>
                                <li>10 GB of storage</li>
                                <li>Priority email support</li>
                                <li>Help center access</li>
                            </ul>
                            <button type="button" class="w-100 btn btn-lg btn-primary">Get started</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4 rounded-3 shadow-sm">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Enterprise</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">$29 <small class="text-muted fw-light">/
                                    mo</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>30 users included</li>
                                <li>15 GB of storage</li>
                                <li>Phone and email support</li>
                                <li>Help center access</li>
                            </ul>
                            <button type="button" class="w-100 btn btn-lg btn-primary">Contact us</button>
                        </div>
                    </div>
                </div>
            </div>

            <?= view('partials/footer') ?>
        </div>
    </main>
</body>

</html>