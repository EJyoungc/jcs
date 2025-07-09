<?php
namespace App\Livewire\Repots;

use App\Models\PurchaseRequest;
use App\Models\Bid;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ReportLive extends Component
{
    use LivewireAlert;

    public $prStatusCounts = [];
    public $bidStatusCounts = [];
    public $stockLevels = [];

    public function mount()
    {
        // Purchase Requests by Status
        $this->prStatusCounts = PurchaseRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Bids by Status
        $this->bidStatusCounts = Bid::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Inventory: counts of items below minimum vs above
        $below = InventoryItem::whereColumn('current_stock', '<', 'minimum_stock')->count();
        $above = InventoryItem::whereColumn('current_stock', '>=', 'minimum_stock')->count();
        $this->stockLevels = [
            'Below Minimum' => $below,
            'Sufficient'    => $above,
        ];
    }

    public function render()
    {
        return view('livewire.repots.report-live');
    }
}
