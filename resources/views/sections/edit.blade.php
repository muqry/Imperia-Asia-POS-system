<div class="modal fade closeModel" id="editSection" wire:ignore.self data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                    <h3 class="modal-title">Edit Section</h3>
                    <button class="close" data-dismiss="modal">&times;</button>                
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="update({{ $section->id }})" method="post" autocomplete="off">
                @csrf        
                
                    <div class="form-row">
                        <div class="col-md-9">
                            <label for="">Section Name</label>
                            <input type="text" wire:model="section_name" id="section_name" class="form-control" autocomplete="off">
                            @error('section_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status Checkbox -->
                            <div class="col-sm-1" data-toggle="tooltip" data-placement="top" title="status">
                                <label class="switch" style="margin-top:2.2em !important">
                                    <input type="checkbox" wire:model="section_status" id="section_status">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            Update Section
                        </button>
                        <button type="button" class="btn btn-danger w-100" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    margin-bottom: 0.5rem;
}

.switch input {
    opacity: 0;
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    cursor: pointer;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider::before {
    position: absolute;
    content: '';
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider::before {
    transform: translateX(26px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round::before {
    border-radius: 50%;
}

.form-row {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
}

.form-row .col,
.form-row .col-sm-1 {
    flex: none;
}

.btn {
    margin-bottom: 0.5rem;
}
</style>