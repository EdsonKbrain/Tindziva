<x-mail::message>
# {{ $isQuote ? 'Novo pedido de proposta' : 'Nova mensagem de contacto' }}

Foi recebido um novo {{ $isQuote ? 'pedido de proposta' : 'contacto' }} através do website.

<x-mail::table>
| Campo | Valor |
|:--|:--|
| Nome | {{ $lead->name }} |
@if ($lead->company)
| Empresa | {{ $lead->company }} |
@endif
| E-mail | {{ $lead->email }} |
@if ($lead->phone)
| Telefone | {{ $lead->phone }} |
@endif
@if ($isQuote)
| Serviço | {{ $lead->service_name ?? 'Não indicado' }} |
@if ($lead->budget)
| Orçamento | {{ $lead->budget }} |
@endif
@if ($lead->deadline)
| Prazo | {{ $lead->deadline->format('d/m/Y') }} |
@endif
@elseif ($lead->subject)
| Assunto | {{ $lead->subject }} |
@endif
</x-mail::table>

**Mensagem**

{{ $lead->message }}

<x-mail::button :url="$adminUrl" color="primary">
Abrir no painel
</x-mail::button>

Responda directamente a este e-mail para contactar {{ $lead->name }}.
</x-mail::message>
