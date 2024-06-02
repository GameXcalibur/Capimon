<a href="{{ route('index.site', $data->id) }}" class="btn btn-success btn-sm" title='View Site Assets'>
    <i class="bi bi-eye"></i>
</a>
<a href="{{ route('product-categories.edit', $data->id) }}" class="btn btn-info btn-sm" title='Edit Site'>
    <i class="bi bi-pencil"></i>
</a>

<button id="delete" class="btn btn-danger btn-sm" onclick="
    event.preventDefault();
    if (confirm('Are you sure? It will delete the data permanently!')) {
        document.getElementById('destroy{{ $data->id }}').submit();
    }
    " title='Delete Site'>
    <i class="bi bi-trash"></i>
    <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('product-categories.destroy', $data->id) }}" method="POST">
        @csrf
        @method('delete')
    </form>
</button>
