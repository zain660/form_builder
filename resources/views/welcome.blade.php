{{-- 
    Smooth Interact.js Form Builder

    This Blade template provides a drag-and-drop form builder interface using Interact.js and Bootstrap 5.
    Users can drag field types from a toolbox into a form area, reposition, and resize them.
    Features:
    - Toolbox with various field types (text, textarea, select, checkbox, radio, button, date, file).
    - Drag fields from toolbox to form area with ghost preview.
    - Fields are draggable and resizable within the form area.
    - Each field includes a delete handle for removal.
    - "Save Form JSON" button outputs the current form layout as JSON.
    - "Clear Form" button removes all fields from the form area.
    - Responsive layout using Bootstrap grid.
    - Custom styles for smooth UX.

    Key JavaScript Functions:
    - fieldMarkup(type): Returns HTML markup for each field type.
    - createField(type, left, top): Creates a draggable, resizable field at specified position.
    - Interact.js handles drag, drop, and resize interactions.
    - Save and clear actions manage the form state and output.

    Usage:
    - Drag items from the toolbox into the form area.
    - Move and resize fields as needed.
    - Save or clear the form using provided buttons.

--}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smooth Interact.js Form Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: system-ui, Arial, sans-serif;
        }

        #toolbox {
            border: 1px dashed #dee2e6;
            padding: 12px;
            background: #f8f9fa;
            height: 100%;
        }

        .toolbox-item {
            background: #e9ecef;
            border: 1px solid #ced4da;
            padding: 8px 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            cursor: grab;
            user-select: none;
            text-align: center
        }

        #form-area {
            border: 2px dashed #0d6efd;
            min-height: 520px;
            position: relative;
            background: #fff;
            overflow: hidden;
        }

        .field {
            position: absolute;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        .field .delete-handle {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer
        }

        #output {
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin-top: 12px
        }

        .ghost-clone {
            position: fixed;
            pointer-events: none;
            opacity: 0.6;
            z-index: 1000;
            border: 2px dashed #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
            border-radius: 6px;
            background: #f0f8ff;
        }
    </style>
</head>

<body class="container py-4">
    <h3 class="mb-3">
        <input type="text" name="form_name" class="form-control" id="form_name" placeholder="Enter Form Name"
            required />
    </h3>

    <div class="row">
        <div class="col-md-2">
            <div id="toolbox">
                <div class="toolbox-item" data-type="text">Text Input</div>
                <div class="toolbox-item" data-type="textarea">Textarea</div>
                <div class="toolbox-item" data-type="select">Dropdown</div>
                <div class="toolbox-item" data-type="checkbox">Checkbox</div>
                <div class="toolbox-item" data-type="radio">Radio</div>
                <div class="toolbox-item" data-type="button">Button</div>
                <div class="toolbox-item" data-type="date">Date</div>
                <div class="toolbox-item" data-type="file">File</div>
                <div class="toolbox-item" data-type="label">Label</div>

            </div>
        </div>
        <div class="col-md-8">
            <div id="form-area" class="mb-2">
                <div class="p-3 text-muted">Drop items from the toolbox. Drag inside to move. Resize from edges/corners.
                </div>
            </div>

            <div class="mb-3">
                <button id="saveForm" class="btn btn-primary">Save Form JSON</button>
                <button id="clearForm" class="btn btn-outline-secondary ms-2">Clear Form</button>
            </div>
            @csrf
            <pre id="output"></pre>
        </div>
         <div class="col-md-2">
            <div id="toolbox">
                <h4>All Forms</h4>
                <hr>
                @foreach ($forms as $form)
                    <div class="toolbox-item" data-type="text">
                        <a href="{{ route('forms.show', $form->id) }}">{{ $form->name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function fieldMarkup(type) {
            if (type === 'text')
                return '<label class="form-label" contenteditable="true">Text Input</label><input type="text" class="form-control" placeholder="Text">'

            if (type === 'textarea')
                return '<label class="form-label" contenteditable="true">Textarea</label><textarea class="form-control" placeholder="Textarea"></textarea>'

            if (type === 'select')
                return '<label class="form-label" contenteditable="true">Dropdown</label><select class="form-select"><option>Option 1</option><option>Option 2</option></select>'

            if (type === 'checkbox')
                return '<div class="form-check"><input class="form-check-input" type="checkbox" id="chk_' + Date.now() +
                    '"><label class="form-check-label" contenteditable="true">Checkbox</label></div>'

            if (type === 'radio')
                return '<div class="form-check"><input class="form-check-input" type="radio" id="rd_' + Date.now() +
                    '" name="radioGroup"><label class="form-check-label" contenteditable="true">Radio</label></div>'

            if (type === 'button')
                return '<label class="form-label" contenteditable="true">Button</label><button class="btn btn-secondary">Button</button>'

            if (type === 'date')
                return '<label class="form-label" contenteditable="true">Date</label><input type="date" class="form-control">'

            if (type === 'file')
                return '<label class="form-label" contenteditable="true">File Upload</label><input type="file" class="form-control">'

            if (type === 'label')
                return '<label class="form-label" contenteditable="true">Label Text</label>'

            if (type === 'heading')
                return '<h3 contenteditable="true">Form Heading</h3>'

            if (type === 'paragraph')
                return '<p contenteditable="true">Form description goes here...</p>'

            return '<div>Unknown</div>'
        }
        let ghost = null

        interact('.toolbox-item').draggable({
            inertia: true,
            autoScroll: true,
            listeners: {
                start(event) {
                    const type = event.target.dataset.type
                    ghost = document.createElement('div')
                    ghost.className = 'ghost-clone field'
                    ghost.style.width = '200px'
                    ghost.style.height = '60px'
                    ghost.innerHTML = fieldMarkup(type)
                    document.body.appendChild(ghost)
                },
                move(event) {
                    const target = event.target
                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
                    const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

                    target.style.transform = `translate(${x}px, ${y}px)`
                    target.setAttribute('data-x', x)
                    target.setAttribute('data-y', y)

                    if (ghost) {
                        ghost.style.left = event.pageX - 100 + 'px'
                        ghost.style.top = event.pageY - 30 + 'px'
                    }
                },
                end(event) {

                    if (ghost) ghost.remove()
                    ghost = null
                    // revert visual transform so toolbox item returns to its place
                    event.target.style.transform = ''
                    event.target.removeAttribute('data-x')
                    event.target.removeAttribute('data-y')
                }
            }
        })

        // --- Form area dropzone ---
        interact('#form-area').dropzone({
            accept: '.toolbox-item',
            overlap: 0.25,
            ondrop(event) {
                const type = event.relatedTarget.dataset.type
                const container = event.target
                const containerRect = container.getBoundingClientRect()

                // Pointer position at drop time
                const pageX = event.dragEvent.clientX
                const pageY = event.dragEvent.clientY

                // Position relative to container
                const left = Math.max(0, pageX - containerRect.left)
                const top = Math.max(0, pageY - containerRect.top)

                createField(type, left, top)
            }
        })

        // --- Create field inside form-area ---
        function createField(type, left, top) {
            const container = document.getElementById('form-area')

            const field = document.createElement('div')
            field.className = 'field'
            field.dataset.type = type
            field.style.left = (left - 20) + 'px' // offset so cursor is near center
            field.style.top = (top - 20) + 'px'
            field.style.width = '220px'
            field.style.height = '60px'
            field.innerHTML = fieldMarkup(type)

            // delete handle
            const del = document.createElement('div')
            del.className = 'delete-handle'
            del.innerHTML = '×'
            del.title = 'Delete'
            del.addEventListener('click', (e) => {
                e.stopPropagation();
                field.remove()
            })
            field.appendChild(del)

            container.appendChild(field)

            // Make it draggable inside parent by adjusting left/top directly
            interact(field).draggable({
                inertia: true,
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                listeners: {
                    move(event) {
                        const target = event.target
                        const curLeft = parseFloat(target.style.left) || 0
                        const curTop = parseFloat(target.style.top) || 0
                        target.style.left = (curLeft + event.dx) + 'px'
                        target.style.top = (curTop + event.dy) + 'px'
                    }
                }
            })

            // Make it resizable and keep it inside parent
            interact(field).resizable({
                edges: {
                    left: true,
                    right: true,
                    bottom: true,
                    top: true
                },
                modifiers: [
                    interact.modifiers.restrictEdges({
                        outer: 'parent'
                    }),
                    interact.modifiers.restrictSize({
                        min: {
                            width: 60,
                            height: 30
                        }
                    })
                ],
                listeners: {
                    move(event) {
                        const target = event.target

                        // update size
                        target.style.width = event.rect.width + 'px'
                        target.style.height = event.rect.height + 'px'

                        // update position if top/left edges were moved
                        const left = (parseFloat(target.style.left) || 0) + event.deltaRect.left
                        const top = (parseFloat(target.style.top) || 0) + event.deltaRect.top
                        target.style.left = left + 'px'
                        target.style.top = top + 'px'
                    }
                }
            })
        }

        // --- Save form JSON ---
        document.getElementById('saveForm').addEventListener('click', () => {
            const list = []
            document.querySelectorAll('#form-area .field').forEach(el => {
                list.push({
                    type: el.dataset.type,
                    left: parseFloat(el.style.left) || 0,
                    top: parseFloat(el.style.top) || 0,
                    width: parseFloat(el.style.width) || el.getBoundingClientRect().width,
                    height: parseFloat(el.style.height) || el.getBoundingClientRect().height,
                    text: el.querySelector('[contenteditable]') ?
                        el.querySelector('[contenteditable]').innerText :
                        null
                })
            })
            document.getElementById('output').textContent = JSON.stringify(list, null, 2)
        })

        // --- Clear form ---
        document.getElementById('clearForm').addEventListener('click', () => {
            document.querySelectorAll('#form-area .field').forEach(el => el.remove())
            document.getElementById('output').textContent = ''
        })



        document.getElementById('saveForm').addEventListener('click', () => {
            const list = []
            document.querySelectorAll('#form-area .field').forEach(el => {
                list.push({
                    type: el.dataset.type,
                    left: parseFloat(el.style.left) || 0,
                    top: parseFloat(el.style.top) || 0,
                    width: parseFloat(el.style.width) || el.getBoundingClientRect().width,
                    height: parseFloat(el.style.height) || el.getBoundingClientRect().height,
                    text: el.querySelector('[contenteditable]') ?
                        el.querySelector('[contenteditable]').innerText :
                        null
                })
            })

            fetch('{{ route('forms.formstore') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').
                        getAttribute('content')

                    },
                    body: JSON.stringify({
                        name: $('#form_name').val(),
                        schema: list
                    })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        title: 'Success',
                        text: 'Form saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    })
                })
        })
    </script>


</body>

</html>
