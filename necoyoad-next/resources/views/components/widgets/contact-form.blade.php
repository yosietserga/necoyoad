{{--
    New Necoyoad — ContactForm Widget Template
    Uses Livewire for form submission.
--}}
<li id="{{ $widgetName }}" class="widget contact-form nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    @if (!empty($heading))
        <div class="header"><h3>{{ $heading }}</h3></div>
    @endif
    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
        @csrf
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" required rows="4"></textarea>
        <button type="submit">Send Message</button>
    </form>
</li>
