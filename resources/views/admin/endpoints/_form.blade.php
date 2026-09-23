<label for="name">Name</label>
<input id="name" name="name" value="{{ old('name', $endpoint->name ?? '') }}" required>

<label for="slug">Slug</label>
<input id="slug" name="slug" value="{{ old('slug', $endpoint->slug ?? '') }}" required pattern="[a-z0-9-]+">

<label for="description">Description</label>
<textarea id="description" name="description" rows="3">{{ old('description', $endpoint->description ?? '') }}</textarea>

<label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $endpoint->is_active ?? true))> Active</label>

<label for="fallback_status">Fallback status</label>
<input id="fallback_status" type="number" name="fallback_status" value="{{ old('fallback_status', $endpoint->fallback_status ?? config('mock.fallback_status')) }}">

<label for="fallback_body">Fallback body</label>
<textarea id="fallback_body" name="fallback_body" rows="4">{{ old('fallback_body', $endpoint->fallback_body ?? config('mock.fallback_body')) }}</textarea>

<label for="fallback_headers">Fallback headers (JSON object)</label>
<textarea id="fallback_headers" name="fallback_headers" rows="3">{{ old('fallback_headers', isset($endpoint) && $endpoint->fallback_headers ? json_encode($endpoint->fallback_headers, JSON_PRETTY_PRINT) : json_encode(config('mock.fallback_headers'), JSON_PRETTY_PRINT)) }}</textarea>

@if ($errors->any())
    <div class="alert" style="background:#fef2f2;border-color:#fca5a5;">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
