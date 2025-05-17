{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
  @if(Auth::user()->rol !== 'admin')
    {{-- Bienvenida estilizada y centrada con borde azul intenso --}}
    <div class="d-flex justify-content-center mt-5">
      <div class="p-5 border border-primary rounded-4 text-center"
           style="background-color: #e8f4ff; max-width: 800px; width: 100%;">
        <h1 class="display-1 text-primary mb-2" style="font-weight: 600;">
          ¡Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}!
        </h1>
        <p class="lead text-primary">Nos alegra verte de nuevo en Naturagym</p>
      </div>
    </div>

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

    // Convertimos a objeto Date válido
    const expDate = new Date(expString.replace(' ', 'T'));

    function update() {
      const now = new Date();

      // Calculamos días usando fechas truncadas a medianoche
      const todayUTC = Date.UTC(now.getFullYear(), now.getMonth(), now.getDate());
      const expUTC   = Date.UTC(expDate.getFullYear(), expDate.getMonth(), expDate.getDate());
      const diffDays = Math.ceil((expUTC - todayUTC) / (1000 * 60 * 60 * 24));

      if (diffDays <= 0) {
        countdownEl.textContent = '¡Expirada!';
        countdownEl.classList.replace('text-success', 'text-danger');
        clearInterval(interval);
        return;
      }

      countdownEl.textContent = diffDays;
    }

    const interval = setInterval(update, 1000);
    update();
  });
</script>
@endpush
