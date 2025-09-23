<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Admin - Allo Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .test-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-test { margin: 5px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="test-card p-4">
                    <h1 class="text-center mb-4">
                        <i class="fas fa-shopping-cart text-success"></i>
                        Test Interface Admin
                    </h1>

                    <div class="row">
                        <div class="col-md-6">
                            <h3>Test des Routes Admin</h3>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-test" onclick="testRoute('/admin')">
                                    <i class="fas fa-tachometer-alt"></i> Tableau de Bord
                                </button>
                                <button class="btn btn-success btn-test" onclick="testRoute('/admin/users')">
                                    <i class="fas fa-users"></i> Utilisateurs
                                </button>
                                <button class="btn btn-info btn-test" onclick="testRoute('/admin/products')">
                                    <i class="fas fa-box"></i> Produits
                                </button>
                                <button class="btn btn-warning btn-test" onclick="testRoute('/admin/orders')">
                                    <i class="fas fa-shopping-bag"></i> Commandes
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h3>Test de l'API</h3>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-test" onclick="testApi('/api/v1/products')">
                                    <i class="fas fa-box"></i> API Produits
                                </button>
                                <button class="btn btn-outline-success btn-test" onclick="testApi('/api/v1/categories')">
                                    <i class="fas fa-tags"></i> API Catégories
                                </button>
                                <button class="btn btn-outline-info btn-test" onclick="testApi('/api/v1/orders')">
                                    <i class="fas fa-shopping-bag"></i> API Commandes
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="result" class="mt-4" style="display: none;">
                        <h5>Résultat :</h5>
                        <pre id="result-content" class="bg-light p-3 rounded"></pre>
                    </div>

                    <div class="text-center mt-4">
                        <a href="/admin/login" class="btn btn-success">
                            <i class="fas fa-sign-in-alt"></i> Interface Admin
                        </a>
                        <a href="/" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Page d'Accueil
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

        async function testRoute(route) {
            try {
                const response = await fetch(route);
                const text = await response.text();
                showResult({
                    status: response.status,
                    statusText: response.statusText,
                    content: text.substring(0, 500) + '...'
                });
            } catch (error) {
                showResult({error: error.message});
            }
        }

        async function testApi(endpoint) {
            try {
                const response = await fetch(endpoint);
                const data = await response.json();
                showResult({
                    status: response.status,
                    statusText: response.statusText,
                    data: data
                });
            } catch (error) {
                showResult({error: error.message});
            }
        }
    </script>
</body>
</html>
