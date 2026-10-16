@foreach(config('blotter.incident_types') as $group => $incidentTypes)
  <optgroup label="{{ $group }}">
    @foreach($incidentTypes as $incidentType)
      <option value="{{ $incidentType['value'] }}" @selected(old('incident_type') === $incidentType['value'])>{{ $incidentType['label'] }}</option>
    @endforeach
  </optgroup>
@endforeach
