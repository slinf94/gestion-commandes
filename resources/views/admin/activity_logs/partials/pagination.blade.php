@if($activities->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <small class="text-muted">
            Affichage de {{ $activities->firstItem() }} à {{ $activities->lastItem() }} 
            sur {{ $activities->total() }} résultats
        </small>
    </div>
    <div>
        {{ $activities->appends(request()->query())->links() }}
    </div>
</div>
@endif


