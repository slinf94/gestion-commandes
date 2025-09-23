<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - Allo Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .api-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .endpoint { background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .method { font-weight: bold; padding: 4px 8px; border-radius: 4px; color: white; }
        .get { background: #28a745; }
        .post { background: #007bff; }
        .put { background: #ffc107; color: #000; }
        .delete { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="api-card p-4">
                    <h1 class="text-center mb-4">
                        <i class="fas fa-shopping-cart text-success"></i>
                        Allo Mobile - Test API
                    </h1>

                    <div class="row">
                        <div class="col-md-6">
                            <h3>Endpoints API Disponibles</h3>

                            <div class="endpoint">
                                <span class="method get">GET</span>
                                <strong>/api/v1/products</strong>
                                <p class="mb-0 text-muted">Récupérer tous les produits</p>
                            </div>

                            <div class="endpoint">
                                <span class="method get">GET</span>
                                <strong>/api/v1/products/featured</strong>
                                <p class="mb-0 text-muted">Produits en vedette</p>
                            </div>

                            <div class="endpoint">
                                <span class="method get">GET</span>
                                <strong>/api/v1/categories</strong>
                                <p class="mb-0 text-muted">Récupérer toutes les catégories</p>
                            </div>

                            <div class="endpoint">
                                <span class="method post">POST</span>
                                <strong>/api/v1/auth/login</strong>
                                <p class="mb-0 text-muted">Connexion utilisateur</p>
                            </div>

                            <div class="endpoint">
                                <span class="method post">POST</span>
                                <strong>/api/v1/auth/register</strong>
                                <p class="mb-0 text-muted">Inscription utilisateur</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h3>Test Rapide</h3>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" onclick="testProducts()">
                                    <i class="fas fa-box"></i> Tester les Produits
                                </button>
                                <button class="btn btn-info" onclick="testCategories()">
                                    <i class="fas fa-tags"></i> Tester les Catégories
                                </button>
                                <button class="btn btn-warning" onclick="testFeatured()">
                                    <i class="fas fa-star"></i> Tester les Produits Vedette
                                </button>
                                <button class="btn btn-primary" onclick="testLogin()">
                                    <i class="fas fa-sign-in-alt"></i> Tester la Connexion
                                </button>
                            </div>

                            <div id="result" class="mt-3" style="display: none;">
                                <h5>Résultat :</h5>
                                <pre id="result-content" class="bg-light p-3 rounded"></pre>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="/admin/login" class="btn btn-outline-success">
                            <i class="fas fa-cog"></i> Interface Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showResult(data) {
            document.getElementById('result').style.display = 'block';
            document.getElementById('result-content').textContent = JSON.stringify(data, null, 2);
        }

        async function testProducts() {
            try {
                const response = await fetch('/api/v1/products');
                const data = await response.json();
                showResult(data);
            } catch (error) {
                showResult({error: error.message});
            }
        }

        async function testCategories() {
            try {
                const response = await fetch('/api/v1/categories');
                const data = await response.json();
                showResult(data);
            } catch (error) {
                showResult({error: error.message});
            }
        }

        async function testFeatured() {
            try {
                const response = await fetch('/api/v1/products/featured');
                const data = await response.json();
                showResult(data);
            } catch (error) {
                showResult({error: error.message});
            }
        }

        async function testLogin() {
            try {
                const response = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: 'admin@example.com',
                        password: 'password'
                    })
                });
                const data = await response.json();
                showResult(data);
            } catch (error) {
                showResult({error: error.message});
            }
        }
    </script>
</body>
</html>

