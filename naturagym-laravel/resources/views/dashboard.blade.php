{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
  @if(Auth::user()->rol !== 'admin')
    {{-- WRAPPER RESPONSIVO: fondo blanco en móvil, azul pálido en desktop --}}
    <div class="bg-white md:bg-[#f0f8ff] md:text-[#212529] min-vh-100 pb-5">
      
      {{-- Bienvenida estilizada y centrada con borde azul intenso --}}
      <div class="d-flex justify-content-center mt-5">
        <div class="p-5 border border-primary rounded-4 text-center"
             style="background-color: inherit; max-width: 800px; width: 100%;">
          <h1 class="display-1 text-primary mb-2" style="font-weight: 600;">
            ¡Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}!
          </h1>
          <p class="lead text-primary">Nos alegra verte de nuevo en Naturagym</p>
        </div>
      </div>

      {{-- Comprueba primero si existe fecha de expiración --}}
      @if(Auth::user()->subscription_expires_at)
        {{-- Sección de suscripción con cuenta atrás en JavaScript --}}
        <div id="subscription"
             class="d-flex justify-content-center mt-4"
             data-expiration="{{ Auth::user()->subscription_expires_at->format('Y-m-d H:i:s') }}">
          <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
            <div class="card-body text-center">
              <p class="mb-3">
                Tu suscripción expira el:
                <strong>{{ Auth::user()->subscription_expires_at->format('d/m/Y H:i') }}</strong>
              </p>
              <h5 class="card-title mb-3">Días restantes de suscripción</h5>
              <p id="countdown" class="display-4 mb-0 text-success">Cargando...</p>
            </div>
          </div>
        </div>
      @else
        {{-- Mensaje si no tiene suscripción --}}
        <div class="d-flex justify-content-center mt-4">
          <div class="card shadow-sm text-center" style="max-width: 400px; width: 100%;">
            <div class="card-body">
              <p class="text-warning mb-0">No tienes una suscripción activa.</p>
            </div>
          </div>
        </div>
      @endif

    </div>
  @endif

  {{-- Resto del contenido del dashboard --}}
  <div class="container mt-5">
    {{-- ... aquí tu contenido ... --}}
  </div>
@endsection

@push('styles')
<style>
  /* Ajustes personalizados */
  .display-1 {
    font-size: 3.5rem;
  }
  .display-4 {
    font-size: 2.5rem;
  }
  /* Asegura que el wrapper cubra al menos la altura de pantalla */
  .min-vh-100 {
    min-height: 100vh;
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const container   = document.getElementById('subscription');
    const countdownEl = document.getElementById('countdown');
    if (!container || !countdownEl) return;

    let expString = container.dataset.expiration;
    if (!expString) {
      countdownEl.textContent = 'Sin fecha disponible';
      countdownEl.classList.replace('text-success', 'text-warning');
      return;
    }

    // Creamos la fecha de expiración
    const expDate = new Date(expString.replace(' ', 'T'));

    function update() {
      const now = new Date();

      // Truncamos ambas fechas a medianoche local
      const today  = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const expiry = new Date(expDate.getFullYear(), expDate.getMonth(), expDate.getDate());

      // Calculamos diferencia en milisegundos y la convertimos a días
      const diffMs   = expiry - today;
      const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

      if (diffDays <= 0) {
        countdownEl.textContent = '¡Expirada!';
        countdownEl.classList.replace('text-success', 'text-danger');
        clearInterval(interval);
      } else {
        countdownEl.textContent = diffDays + (diffDays === 1 ? ' día' : ' días');
      }
    }

    const interval = setInterval(update, 1000);
    update();
  });
</script>
@endpush
