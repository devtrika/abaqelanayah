<!-- Start Footer -->
<footer class="main-footer">
  <div class="container">
    <div class="footer">
      <div class="row">
        {{-- Footer Logo & Social Media --}}
        <div class="col-lg-4 col-12">
          <div class="footer-information">
            <div class="footer-logo">
              <img
                loading="lazy"
                src="{{$settings['logo']}}"
                alt="logo"
                class="img-contain"
              />
            </div>
            <div class="footer-socials">
              @php
                $socials = \App\Models\Social::all()->keyBy('name');
                $telegram = $socials['telegram']->link ?? '#!';
                $youtube = $socials['youtube']->link ?? '#!';
                $instagram = $socials['instagram']->link ?? '#!';
                $twitter = $socials['twitter']->link ?? '#!';
                $facebook = $socials['facebook']->link ?? '#!';
              @endphp
              <a href="{{ $telegram }}" target="_blank" class="social-link" aria-label="Telegram">
                <i class="fab fa-telegram"></i>
              </a>
              <a href="{{ $youtube }}" target="_blank" class="social-link" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
              </a>
              <a href="{{ $instagram }}" target="_blank" class="social-link" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="{{ $twitter }}" target="_blank" class="social-link" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="{{ $facebook }}" target="_blank" class="social-link" aria-label="Facebook">
                <i class="fab fa-facebook"></i>
              </a>
            </div>
          </div>
        </div>
        
        {{-- Important Links --}}
        <div class="col-lg-5 col-md-7">
          <div class="footer-menu">
            <h3 class="footer-title">{{ __('site.quick_links') }}</h3>
            <ul class="footer-list">
              <li>
                <a href="{{ route('website.about') }}">{{ __('site.about') }}</a>
              </li>
              <li>
                <a href="{{ route('website.faq') }}">{{ __('site.fqs') }}</a>
              </li>
              <li>
                <a href="{{ route('website.terms') }}">{{ __('site.terms_and_conditions') }}</a>
              </li>
              <li>
                <a href="{{ route('website.privacy') }}">{{ __('site.privacy') }}</a>
              </li>
              <li>
                <a href="{{ route('website.returns') }}">{{ __('site.return_policy') }}</a>
              </li>
              <li>
                <a href="{{ route('website.contact') }}">{{ __('site.contact_us') }}</a>
              </li>
            </ul>
          </div>
        </div>
        
        {{-- Contact Information --}}
        <div class="col-lg-3 col-md-5">
          <div class="footer-menu">
            <h3 class="footer-title">{{ __('site.contact_information') }}</h3>
            <ul class="footer-contacts">
              @php
                $phone = $settings['phone'] ?? '';
                $whatsapp = $settings['whatsapp'] ?? '';
                $email = $settings['email'] ?? '';
              @endphp
              @if($phone)
              <li>
                <a href="tel:{{ $phone }}" class="value">
                  <i class="fal fa-phone"></i>
                  <span class="en">{{ $phone }}</span>
                </a>
              </li>
              @endif
              @if($whatsapp)
              <li>
                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="value">
                  <i class="fab fa-whatsapp"></i>
                  <span class="en">{{ $whatsapp }}</span>
                </a>
              </li>
              @endif
              @if($email)
              <li>
                <a href="mailto:{{ $email }}" class="value">
                  <i class="fal fa-envelope"></i>
                  <span class="en">{{ $email }}</span>
                </a>
              </li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>
   
    {{-- Footer Bottom --}}
    <div class="last-footer">
      <p class="copyrights">{{ __('site.copyright').$settings['name_'.$currentLocale] }}</p>
      <div class="footer-payments">
        <img
          loading="lazy"
          src="{{ asset('website/images/payments.jpg') }}"
          alt="payment"
          class="img-contain"
        />
      </div>
      <p class="copyrights tasawk">
        {{ __('site.developed_by') }}
        <a
          href="https://tasawk.com.sa"
          target="_blank"
          aria-label="tasawk link"
        >
          <img loading="lazy" src="{{ asset('website/images/icons/tasawk.svg') }}" alt="tasawk" />
          {{ __('site.tasawk_it') }}
        </a>
      </p>
    </div>
  </div>
</footer>
<!-- End Footer -->

