@extends('admin.master.main')

@section('content')
<div class="contact-container">
    <div class="card">
        <div class="card-header">
            <h2>Contact Us</h2>
        </div>
        <div class="card-body">
            <p><strong>Company Name:</strong> Naxas</p>
            <p><strong>Address:</strong> Civic Center Beheira Phase 4 block A 44-A Rawalpindi</p>
            <p><strong>Email:</strong> <a href="mailto:abc@gmail.com">abc@gmail.com</a></p>
            <p><strong>Phone:</strong> <a href="tel:+1234567890">+1234567890</a></p>
        </div>
        <!-- <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3309.526704108697!2d73.0479!3d33.6846!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0:0x0!2zMznCsDQxJzA0LjUiTiA3M8KwMDInNTIuOCJF!5e0!3m2!1sen!2s!4v1594102857494!5m2!1sen!2s&zoom=15" 
                width="600" 
                height="450" 
                frameborder="0" 
                style="border:0;" 
                allowfullscreen="" 
                aria-hidden="false" 
                tabindex="0">
            </iframe>
        </div> -->
    </div>
</div>
@endsection

@push('styles')
<style>
    .contact-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f8f9fa;
        padding: 20px;
    }
    .card {
        width: 100%;
        max-width: 600px;
        margin: 20px;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background-color: #ffffff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        background-color: #343a40;
        color: #ffffff;
        text-align: center;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    .card-body {
        padding: 1.25rem;
    }
    .card-body p {
        margin: 0.5rem 0;
    }
    .card-body a {
        color: #007bff;
        text-decoration: none;
    }
    .card-body a:hover {
        text-decoration: underline;
    }
    .map-container {
        padding: 1rem;
    }
    .map-container iframe {
        width: 100%;
        height: 450px;
        border: 0;
    }
</style>
@endpush
