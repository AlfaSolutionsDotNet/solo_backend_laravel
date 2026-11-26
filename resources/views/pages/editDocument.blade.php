@extends('include.app')
@section('content')
<div class="card"><div class="card-header"><h4>{{ $title }}</h4></div><div class="card-body">
    @if (session('document_saved')) <div class="alert alert-success">Document saved.</div> @endif
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <form method="post" action="{{ route('documents.save', $slug) }}">
        @csrf
        <label for="content">Content</label>
        <textarea class="form-control" id="content" name="content" rows="18">{{ old('content', $document->content ?? 'Sample content: Please replace this text before publishing.') }}</textarea>
        <label class="mt-3"><input type="checkbox" name="published" value="1" {{ old('published', $document->published ?? false) ? 'checked' : '' }}> Published</label>
        <div class="mt-3"><button type="submit" class="btn theme-btn text-white">Save</button>
        @if ($document && $document->published) <a href="{{ route('documents.show', $slug) }}" target="_blank" rel="noopener" class="btn btn-secondary">Preview</a> @endif</div>
    </form>
</div></div>
@endsection
