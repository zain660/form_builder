    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


   <div class="container">
     <div class="card">
        <div class="card-header">
            <h2>{{ $form->name }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="#">
                @csrf
                <div class="p-3 border bg-light">
                    {{-- @dd($form); --}}
                    <div class="container">
                        @foreach ($form->schema as $field)
                            {{-- @dd(($field['type'] === 'label')); --}}
                            <label class="form-label">{{ $field['text'] ?? 'Label' }}</label>

                            <div style="margin:10px 0; width:{{ $field['width'] }}px;">
                                @if ($field['type'] === 'text')
                                    <input type="text" class="form-control" name="field_{{ $loop->index }}">
                                @elseif($field['type'] === 'textarea')
                                    <textarea class="form-control" name="field_{{ $loop->index }}"></textarea>
                                @elseif($field['type'] === 'select')
                                    <select class="form-select" name="field_{{ $loop->index }}">
                                        <option>Option 1</option>
                                        <option>Option 2</option>
                                    </select>
                                @elseif($field['type'] === 'checkbox')
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            name="field_{{ $loop->index }}">
                                    </div>
                                @elseif($field['type'] === 'radio')
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="field_{{ $loop->index }}">
                                    </div>
                                @elseif($field['type'] === 'button')
                                    <button type="button" class="btn btn-secondary">Button</button>
                                @elseif($field['type'] === 'date')
                                    <input type="date" class="form-control" name="field_{{ $loop->index }}">
                                @elseif($field['type'] === 'file')
                                    <input type="file" class="form-control" name="field_{{ $loop->index }}">
                                @endif
                                {{-- @dd($field); --}}
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>
   </div>
