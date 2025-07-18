<!-- See https://rapidocweb.com/api.html for options -->
<!doctype html> <!-- Important: must specify -->
<html>
<head>
    <meta charset="utf-8"> <!-- Important: rapi-doc uses utf8 characters -->
    <script type="module" src="https://unpkg.com/rapidoc/dist/rapidoc-min.js"></script>
    <style>
        rapi-doc::part(section-navbar) { 
          background: linear-gradient(90deg, #3d4e70, #2e3746);
        }
      </style>
</head>
<body>
<rapi-doc
@foreach($htmlAttributes as $attribute => $value)
    {!! $attribute !!}="{!! $value !!}"
@endforeach
    spec-url="{!! $metadata['openapi_spec_url'] !!}"
    render-style="focused"
    theme="dark"
    allow-try=false
    show-header="false"
    primary-color = "#f54c47"
    bg-color = "#2e3746"
    text-color = "#bacdee"
    "
>
    @if($metadata['logo'])
        <img slot="logo" src="{!! $metadata['logo'] !!}"/>
    @endif
</rapi-doc>
</body>
</html>
