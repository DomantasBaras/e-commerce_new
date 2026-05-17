@extends('layouts.app')

@section('content')
<div id="app">
    <product-form :id="{{ $id }}"></product-form>
</div>
@endsection
