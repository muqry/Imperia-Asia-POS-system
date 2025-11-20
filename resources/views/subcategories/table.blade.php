
<div class="col-md-4 col-md 4">
    @if (count($checked) > 1)
        <a href="#" class="btn btn-outline btn-sm" wire:click.prevent="confirmBulkDelete">
            ( {{ count($checked) }} Row Selected to <b>Delete</b>)
        </a>
    @endif
</div>

<table class="table" width="100%">

    <thead>
        <tr>
            <th><input class="h-5 w-5" type="checkbox" wire:click="toggleSelectAll"></th>
            <th>Section Name</th>
            <th>Section Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        
            @forelse ($sections as $section)
                <tr>
                    <td><input value="{{ $section->id }}" wire:model="checked" class="h-5 w-5" type="checkbox"></td>
                    <td>{{ $section->section_name }}</td>
                    <td>{{ $section->status == 1 ? "Enable" : 'Disable' }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="#editSection" data-toggle="modal" wire:click.prevent="editSection({{ $section->id }})" class="btn btn-outline-info btn-rounded"><i class="fa fa-edit"></i></a>
                            @if (count($checked) < 2)
                                <a href=""  wire:click.prevent='ConfirmDelete({{ $section->id }}, @json($section->section_name))' class="btn btn-outline-danger btn-rounded"><i class="fa fa-trash"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @include('sections.edit')
            @empty

            @endforelse

    </tbody>

</table>