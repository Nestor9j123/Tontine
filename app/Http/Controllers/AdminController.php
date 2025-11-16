<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use App\Models\Product;
use App\Services\ClientDeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $clientDeletionService;

    public function __construct(ClientDeletionService $clientDeletionService)
    {
        $this->middleware(['auth', 'role:super_admin']);
        $this->clientDeletionService = $clientDeletionService;
    }

    /**
     * Dashboard d'administration
     */
    public function index()
    {
        $stats = [
            'clients' => [
                'active' => Client::count(),
                'deleted' => Client::onlyTrashed()->count(),
            ],
            'tontines' => [
                'active' => Tontine::count(),
                'deleted' => Tontine::onlyTrashed()->count(),
            ],
            'payments' => [
                'active' => Payment::count(),
                'deleted' => Payment::onlyTrashed()->count(),
            ],
            'products' => [
                'active' => Product::where('is_active', true)->count(),
                'inactive' => Product::where('is_active', false)->count(),
            ],
        ];

        // Données orphelines
        $orphanedData = [
            'tontines_without_client' => Tontine::whereDoesntHave('client')->count(),
            'tontines_without_product' => Tontine::whereDoesntHave('product')->count(),
            'payments_without_client' => Payment::whereDoesntHave('client')->count(),
            'payments_without_tontine' => Payment::whereDoesntHave('tontine')->count(),
        ];

        return view('admin.index', compact('stats', 'orphanedData'));
    }

    /**
     * Gestion des clients supprimés
     */
    public function deletedClients()
    {
        $deletedClients = Client::onlyTrashed()
            ->with(['agent'])
            ->withCount(['tontines', 'payments'])
            ->paginate(20);

        return view('admin.deleted-clients', compact('deletedClients'));
    }

    /**
     * Prévisualiser la suppression d'un client
     */
    public function previewClientDeletion(Client $client)
    {
        $preview = $this->clientDeletionService->getDeletionPreview($client);
        return response()->json($preview);
    }

    /**
     * Supprimer définitivement un client
     */
    public function forceDeleteClient(Client $client)
    {
        try {
            $summary = $this->clientDeletionService->deleteClientWithRelatedData($client, true);
            
            return redirect()->back()->with('success', 
                "Client {$summary['client_name']} définitivement supprimé avec {$summary['tontines_count']} tontines et {$summary['payments_count']} paiements."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Restaurer un client supprimé
     */
    public function restoreClient($clientId)
    {
        try {
            $client = Client::onlyTrashed()->findOrFail($clientId);
            $summary = $this->clientDeletionService->restoreClientWithRelatedData($client);
            
            return redirect()->back()->with('success', 
                "Client {$summary['client_name']} restauré avec {$summary['tontines_restored']} tontines et {$summary['payments_restored']} paiements."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    /**
     * Nettoyer les données orphelines
     */
    public function cleanOrphanedData()
    {
        try {
            $orphanedTontines = 0;
            $orphanedPayments = 0;
            
            DB::transaction(function () use (&$orphanedTontines, &$orphanedPayments) {
                // Supprimer les tontines sans client
                $orphanedTontines = Tontine::whereDoesntHave('client')->count();
                Tontine::whereDoesntHave('client')->delete();

                // Supprimer les paiements sans client
                $orphanedPayments = Payment::whereDoesntHave('client')->count();
                Payment::whereDoesntHave('client')->delete();

                // Logger l'activité
                Log::info('Nettoyage des données orphelines effectué', [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'orphaned_tontines' => $orphanedTontines,
                    'orphaned_payments' => $orphanedPayments,
                    'timestamp' => now()
                ]);
            });

            return redirect()->back()->with('success', 
                "Nettoyage terminé : {$orphanedTontines} tontines et {$orphanedPayments} paiements orphelins supprimés."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du nettoyage : ' . $e->getMessage());
        }
    }

    /**
     * Statistiques détaillées
     */
    public function statistics()
    {
        $stats = [
            'database_size' => $this->getDatabaseSize(),
            
            'recent_logs' => 'Consultez les logs Laravel pour voir les activités récentes',
                
            'monthly_stats' => [
                'clients_created' => Client::whereMonth('created_at', now()->month)->count(),
                'tontines_created' => Tontine::whereMonth('created_at', now()->month)->count(),
                'payments_made' => Payment::whereMonth('created_at', now()->month)->count(),
            ]
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Obtenir la taille de la base de données de façon sécurisée
     */
    private function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            
            $result = DB::select("
                SELECT 
                    table_name as `table`, 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
                FROM information_schema.TABLES 
                WHERE table_schema = ?
                ORDER BY (data_length + index_length) DESC
                LIMIT 1
            ", [$databaseName]);
            
            return $result[0] ?? (object)['table' => 'N/A', 'size_mb' => 0];
        } catch (\Exception $e) {
            Log::warning('Erreur lors du calcul de la taille de la base de données', [
                'error' => $e->getMessage()
            ]);
            
            return (object)['table' => 'Erreur', 'size_mb' => 'N/A'];
        }
    }
}
