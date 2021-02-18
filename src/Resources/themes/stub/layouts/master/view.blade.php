@extends('shop::layouts.master')
@push('css')
    <!-- Bootstrap CSS -->
{{--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}
    <link rel="stylesheet" href="<?= phpb_theme_asset('css/style.css') ?>" />
    @if($css)
        <style>{{$css}}</style>
    @endif
@endpush
@section('page_title')
    {{--    {{ $page->title }}--}}
@endsection

@section('content-wrapper')
    {!! $body !!}
@endsection