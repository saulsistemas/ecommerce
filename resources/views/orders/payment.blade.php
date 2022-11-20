<x-app-layout>
<div class="grid grid-cols-5 gap-6 container py-8">

    <div class="col-span-3">
        <div class="bg-white rounded-lg shadow-lg px-6 py-4 mb-6">
            <p class="text-gray-700 uppercase"><span class="font-semibold">Número de orden:</span>
                Orden-{{ $order->id }}</p>
            @if ($order->status == 1)
                <x-button-enlace class="ml-auto" href="{{route('orders.payment', $order)}}">
                    Ir a pagar
                </x-button-enlace>

            @endif
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6 text-gray-700">
                <div>
                    <p class="text-lg font-semibold uppercase">Envío</p>
                    @if ($order->envio_type == 1)
                        <p class="text-sm">Los productos deben ser recogidos en tienda</p>
                        <p class="text-sm">Calle falsa 123</p>
                    @else
                        <p class="text-sm">Los productos Serán enviados a:</p>
                        <p class="text-sm">{{ $envio->address }}</p>
                        <p>{{ $envio->department }} - {{ $envio->city }} - {{ $envio->district }}
                        </p>
                    @endif
                </div>
                <div>
                    <p class="text-lg font-semibold uppercase">Datos de contacto</p>
                    <p class="text-sm">Persona que recibirá el producto: {{ $order->contact }}</p>
                    <p class="text-sm">Teléfono de contacto: {{ $order->phone }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">
            <p class="text-xl font-semibold mb-4">Resumen</p>

            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th>Precio</th>
                        <th>Cant</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($items as $item)
                        <tr>
                            <td>
                                <div class="flex">
                                    <img class="h-15 w-20 object-cover mr-4" src="{{ $item->options->image }}"
                                        alt="">
                                    <article>
                                        <h1 class="font-bold">{{ $item->name }}</h1>
                                        <div class="flex text-xs">

                                            @isset($item->options->color)
                                                Color: {{ __($item->options->color) }}
                                            @endisset

                                            @isset($item->options->size)
                                                - {{ $item->options->size }}
                                            @endisset
                                        </div>
                                    </article>
                                </div>
                            </td>

                            <td class="text-center">
                                {{ $item->price }} USD
                            </td>

                            <td class="text-center">
                                {{ $item->qty }}
                            </td>

                            <td class="text-center">
                                {{ $item->price * $item->qty }} USD
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
    </div>

    <div class="col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-6 flex justify-between text-gray-700 mb-6 items-center">
            <img class="h-8" src="{{asset('img/MC_VI_DI_2-1.jpg')}}" alt="">
            <div class="text-gray-700">
                <p class="text-lg font-semibold uppercase">
                    SubTotal: {{$order->total-$order->shipping_cost}} USD
                </p>
                <p class="text-lg font-semibold uppercase">
                    Envío: {{$order->shipping_cost}} USD
                </p>
                <p class="text-lg font-semibold uppercase">
                    Total: {{$order->total}} USD
                </p>
                <div id="paypal-button-container"></div>
            </div>
        </div>
        
    </div>
</div>
<script src="https://www.paypal.com/sdk/js?client-id={{config('services.paypal.client_id')}}"></script>
<script>
    paypal.Buttons({
      // Sets up the transaction when a payment button is clicked
      createOrder: (data, actions) => {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '{{$order->total}}' // Can also reference a variable or function
            }
          }]
        });
      },
      // Finalize the transaction after payer approval
      onApprove: (data, actions) => {
        return actions.order.capture().then(function(orderData) {
          // Successful capture! For dev/demo purposes:
          console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
          const transaction = orderData.purchase_units[0].payments.captures[0];
          alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
          // When ready to go live, remove the alert and show a success message within this page. For example:
          // const element = document.getElementById('paypal-button-container');
          // element.innerHTML = '<h3>Thank you for your payment!</h3>';
          // Or go to another URL:  actions.redirect('thank_you.html');
        });
      }
    }).render('#paypal-button-container');
  </script>
</x-app-layout>