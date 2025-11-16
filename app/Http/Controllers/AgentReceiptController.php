<?php

namespace App\Http\Controllers;

use App\Services\AgentReceiptService;
use App\Models\User;
use Illuminate\Http\Request;

class AgentReceiptController extends Controller
{
    protected $receiptService;

    public function __construct(AgentReceiptService $receiptService)
    {
        $this->middleware('auth');
        $this->middleware('role:secretary|super_admin')->except(['index', 'myReceipt', 'downloadMyReceipt']);
        $this->receiptService = $receiptService;
    }

    /**
     * Afficher la liste des agents pour générer les reçus
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Si c'est un agent, rediriger vers ses propres reçus
        if (auth()->user()->hasRole('agent')) {
            return $this->myReceipt($request);
        }

        $agentsStats = $this->receiptService->getAllAgentsStats($month, $year);

        return view('agent-receipts.index', compact('agentsStats', 'month', 'year'));
    }


    /**
     * Afficher le reçu détaillé d'un agent
     */
    public function show(User $agent, Request $request)
    {
        if (!$agent->hasRole('agent')) {
            return redirect()->back()->with('error', 'Utilisateur invalide.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $receiptData = $this->receiptService->generateMonthlyReceipt($agent, $month, $year);

        return view('agent-receipts.show', compact('receiptData'));
    }

    /**
     * Télécharger le reçu PDF d'un agent
     */
    public function downloadPdf(User $agent, Request $request)
    {
        if (!$agent->hasRole('agent')) {
            return redirect()->back()->with('error', 'Utilisateur invalide.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        return $this->receiptService->generateReceiptPdf($agent, $month, $year);
    }

    /**
     * Reçu personnel de l'agent connecté
     */
    public function myReceipt(Request $request)
    {
        $agent = auth()->user();
        
        if (!$agent->hasRole('agent')) {
            return redirect()->back()->with('error', 'Accès réservé aux agents.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $receiptData = $this->receiptService->generateMonthlyReceipt($agent, $month, $year);

        return view('agent-receipts.my-receipt', compact('receiptData'));
    }

    /**
     * Télécharger son propre reçu PDF
     */
    public function downloadMyReceipt(Request $request)
    {
        $agent = auth()->user();
        
        if (!$agent->hasRole('agent')) {
            return redirect()->back()->with('error', 'Accès réservé aux agents.');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        return $this->receiptService->generateReceiptPdf($agent, $month, $year);
    }

    /**
     * Générer et télécharger tous les reçus d'agents pour un mois
     */
    public function downloadAllReceipts(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $month = $validated['month'];
        $year = $validated['year'];

        $agents = User::role('agent')->get();
        
        // Créer un ZIP avec tous les reçus
        $zip = new \ZipArchive();
        $zipFileName = storage_path("app/temp/reçus-agents-{$month}-{$year}.zip");
        
        // Créer le répertoire si nécessaire
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            foreach ($agents as $agent) {
                try {
                    $receiptData = $this->receiptService->generateMonthlyReceipt($agent, $month, $year);
                    
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('agent-receipts.monthly-receipt', $receiptData);
                    $pdfContent = $pdf->output();
                    
                    $filename = sprintf('recu-agent-%s-%02d-%d.pdf', 
                        \Illuminate\Support\Str::slug($agent->name), $month, $year);
                    
                    $zip->addFromString($filename, $pdfContent);
                } catch (\Exception $e) {
                    \Log::error("Erreur génération reçu agent {$agent->id}: " . $e->getMessage());
                }
            }
            $zip->close();
            
            return response()->download($zipFileName)->deleteFileAfterSend();
        }
        
        return redirect()->back()->with('error', 'Erreur lors de la génération des reçus.');
    }
}
