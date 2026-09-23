<label for="name">Name</label>
<input id="name" name="name" value="{{ old('name', $rule->name ?? '') }}" required>

<label for="priority">Priority (lower first)</label>
<input id="priority" type="number" name="priority" value="{{ old('priority', $rule->priority ?? 1) }}" required min="1">

<label for="method">Method</label>
<select id="method" name="method" required>
    @foreach (['GET','POST','PUT','PATCH','DELETE','OPTIONS','HEAD'] as $method)
        <option value="{{ $method }}" @selected(old('method', $rule->method ?? 'GET') === $method)>{{ $method }}</option>
    @endforeach
</select>

<label for="path_pattern">Path pattern</label>
<input id="path_pattern" name="path_pattern" value="{{ old('path_pattern', $rule->path_pattern ?? '/users') }}" required>

<label for="path_match_type">Path match type</label>
<select id="path_match_type" name="path_match_type" required>
    @foreach (['exact','starts_with','contains'] as $type)
        <option value="{{ $type }}" @selected(old('path_match_type', isset($rule) ? $rule->path_match_type->value : 'exact') === $type)>{{ $type }}</option>
    @endforeach
</select>

<label for="response_status">Response status</label>
<input id="response_status" type="number" name="response_status" value="{{ old('response_status', $rule->response_status ?? 200) }}" required>

<label for="response_delay">Response delay (ms)</label>
<input id="response_delay" type="number" name="response_delay" value="{{ old('response_delay', $rule->response_delay ?? 0) }}" min="0">

<label for="response_body">Response body</label>
<textarea id="response_body" name="response_body" rows="6">{{ old('response_body', $rule->response_body ?? '') }}</textarea>

<label for="response_headers">Response headers (JSON object)</label>
<textarea id="response_headers" name="response_headers" rows="3">{{ old('response_headers', isset($rule) && $rule->response_headers ? json_encode($rule->response_headers, JSON_PRETTY_PRINT) : '{"Content-Type":"application/json"}') }}</textarea>

<label for="query_conditions">Query conditions (JSON array)</label>
<textarea id="query_conditions" name="query_conditions" rows="3">{{ old('query_conditions', isset($rule) && $rule->query_conditions ? json_encode($rule->query_conditions, JSON_PRETTY_PRINT) : '[]') }}</textarea>

<label for="header_conditions">Header conditions (JSON array)</label>
<textarea id="header_conditions" name="header_conditions" rows="3">{{ old('header_conditions', isset($rule) && $rule->header_conditions ? json_encode($rule->header_conditions, JSON_PRETTY_PRINT) : '[]') }}</textarea>

<label for="body_conditions">Body conditions (JSON array)</label>
<textarea id="body_conditions" name="body_conditions" rows="3">{{ old('body_conditions', isset($rule) && $rule->body_conditions ? json_encode($rule->body_conditions, JSON_PRETTY_PRINT) : '[]') }}</textarea>

<label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active ?? true))> Active</label>

@if ($errors->any())
    <div class="alert" style="background:#fef2f2;border-color:#fca5a5;">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
