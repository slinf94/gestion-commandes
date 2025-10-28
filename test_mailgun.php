<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Notifications\AccountActivatedNotification;
use App\Models\Order;
use App\Notifications\OrderStatusChangedNotification;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST MAILGUN POUR ALLO MOBILE ===\n\n";

// Vérifier la configuration
echo "1. Vérification de la configuration...\n";
$config = config('mail');
$services = config('services');

echo "   Mailer par défaut: " . $config['default'] . "\n";
echo "   Transport Mailgun: " . (isset($config['mailers']['mailgun']) ? 'Configuré' : 'Non configuré') . "\n";
echo "   Services Mailgun: " . (isset($services['mailgun']) ? 'Configuré' : 'Non configuré') . "\n";

if (isset($services['mailgun'])) {
    echo "   Domaine Mailgun: " . $services['mailgun']['domain'] . "\n";
    echo "   Clé API: " . (strlen($services['mailgun']['secret']) > 10 ? 'Configurée' : 'Non configurée') . "\n";
}

echo "\n";

// Test d'envoi d'email simple
echo "2. Test d'envoi d'email simple...\n";
try {
    Mail::raw('Test d\'envoi d\'email depuis Allo Mobile avec Mailgun', function (Message $message) {
        $message->to('alnoreply48@gmail.com')
                ->subject('Test Mailgun - Allo Mobile')
                ->from('alnoreply48@gmail.com', 'Allo Mobile');
    });
    echo "✅ Email de test envoyé avec succès !\n";
    echo "   Vérifiez votre boîte email alnoreply48@gmail.com\n";
} catch (Exception $e) {
    echo "❌ Erreur envoi email: " . $e->getMessage() . "\n";
    echo "   Vérifiez votre configuration Mailgun\n";
}

echo "\n";

// Test de notification d'activation
echo "3. Test de notification d'activation de compte...\n";
try {
    $user = User::first();
    if ($user) {
        $user->notify(new AccountActivatedNotification($user));
        echo "✅ Notification d'activation envoyée à: {$user->email}\n";
        echo "   Sujet: 🎉 Votre compte Allo Mobile a été activé !\n";
    } else {
        echo "❌ Aucun utilisateur trouvé\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur notification activation: " . $e->getMessage() . "\n";
}

echo "\n";

// Test de notification de commande
echo "4. Test de notification de changement de statut...\n";
try {
    $order = Order::first();
    if ($order) {
        $oldStatus = OrderStatus::PENDING;
        $newStatus = OrderStatus::CONFIRMED;
        
        $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus, $newStatus));
        echo "✅ Notification de commande envoyée à: {$order->user->email}\n";
        echo "   Commande: {$order->order_number}\n";
        echo "   Statut: {$oldStatus->getLabel()} → {$newStatus->getLabel()}\n";
    } else {
        echo "❌ Aucune commande trouvée\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur notification commande: " . $e->getMessage() . "\n";
}

echo "\n";

echo "=== RÉSULTAT DU TEST ===\n";
echo "Si tous les tests sont ✅, vos emails fonctionnent avec Mailgun !\n";
echo "Si des erreurs ❌ apparaissent, vérifiez votre configuration.\n\n";

echo "PROCHAINES ÉTAPES:\n";
echo "1. Créez votre compte Mailgun sur https://www.mailgun.com/\n";
echo "2. Récupérez vos clés API\n";
echo "3. Mettez à jour config/services.php\n";
echo "4. Changez 'default' => 'mailgun' dans config/mail.php\n";
echo "5. Relancez ce test\n\n";

echo "Vos clients recevront leurs emails en temps réel !\n";




