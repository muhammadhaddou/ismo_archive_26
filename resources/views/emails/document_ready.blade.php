@component('mail::message')
# Bonjour {{ $docRequest->trainee->first_name }},

Le statut de votre demande de document (**{{ $docRequest->type_document }}**) a été mis à jour.

**Statut actuel :** {{ ucfirst($docRequest->status) }}

@if($docRequest->admin_message)
**Message de l'administration :**
{{ $docRequest->admin_message }}
@endif

@if($docRequest->status === 'planifie' && $docRequest->appointment_date)
**Date de rendez-vous :** {{ \Carbon\Carbon::parse($docRequest->appointment_date)->format('d/m/Y à H:i') }}
@endif

Merci,
L'équipe {{ config('app.name') }}
@endcomponent
