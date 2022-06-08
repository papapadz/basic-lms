@component('mail::message')

@component('mail::panel')

@component('mail::promotion')

<div style="font-size: 2rem; text-align:center">
    <b style="color: green">Your Certificate is Ready!</b>
</div>
@endcomponent

<ul class="list-group list-group-flush">
    <li>LDI Title: <b>{{ $data['title'] }}</b></li>
    <li>LDI Date: <b>{{ $data['date'] }}</b></li>
</ul>

@component('mail::button', ['url' => $data['certificate_url'], 'color' => 'green'])
    View Certificate
@endcomponent

@endcomponent

<hr>
<i>*This is an auto generated message, do not reply </i>
<i>*For inquiries, contact IHOMP Unit Office at local 4112</i>
@endcomponent