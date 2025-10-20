<tr data-id="{{ $category->id }}" style="padding-left: {{ $level * 20 }}px;">
    <td>
        <div class="d-flex align-items-center">
            <span class="sort-handle me-2" style="cursor: move;">
                <i class="fas fa-grip-vertical text-muted"></i>
            </span>
            {{ $loop->iteration }}
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            @if($category->icon)
                <i class="{{ $category->icon }} me-2"></i>
            @endif
            <strong>{{ $category->name }}</strong>
            @if($level > 0)
                <span class="badge bg-light text-dark ms-2">Sous-catégorie</span>
            @endif
        </div>
    </td>
    <td><code>{{ $category->slug }}</code></td>
    <td>
        @if($category->parent)
            <span class="badge bg-info">{{ $category->parent->name }}</span>
        @else
            <span class="text-muted">Principal</span>
        @endif
    </td>
    <td>
        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
            {{ $category->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        @if($category->is_featured)
            <i class="fas fa-star text-warning"></i>
        @else
            <i class="fas fa-star text-muted"></i>
        @endif
    </td>
    <td>{{ $category->sort_order }}</td>
    <td>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            <button type="button" class="btn btn-sm btn-outline-info toggle-status-btn"
                    data-id="{{ $category->id }}"
                    title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}">
                <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }}"></i>
            </button>
        </div>
    </td>
</tr>

