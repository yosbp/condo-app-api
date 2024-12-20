<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

<body>
    <div style="position: relative; padding: 1.25rem; border-radius: 0.375rem; background-color: #ffffff; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)">
        <div style="padding-left: 1rem; padding-right: 1rem; gap: 1rem;">
            <div style="float: left; font-size: 1.125rem; line-height: 1.75rem; font-weight: 600; text-transform: uppercase;">
                Invoice
            </div>
            <div style="float: right;">
                <img src="{{ public_path('imgs/light-logo.png') }}" alt="logo" style="width: 3.5rem; display: block; margin-left: auto;">
            </div>
            <div style="clear: both;"></div>
        </div>
        <div style="padding-left: 1rem;padding-right: 1rem; text-align: right; ">
            <div style="margin-top: 1.5rem; margin-top: 0.25rem; color: #888ea8; font-size: 14px">
                <div>13 Tetrick Road, Cypress Gardens, Florida, 33884, US</div>
                <div>vristo@gmail.com</div>
                <div>+1 (070) 123-4567</div>
            </div>
        </div>

        <hr style="margin-top: 0.75rem; margin-bottom: 0.75rem; border-color: #e0e6ed;" />
        <div style="margin-bottom: 1.25rem; overflow: hidden;">
            <!-- Issue For -->
            <div style="float: left; width: 50%;">
                <div style="margin-top: 0.25rem; color: #888ea8; font-size: 14px;">
                    <div>Issue For:</div>
                    <div style="font-weight: 600; color: #000000;">John Doe</div>
                    <div>405 Mulberry Rd. Mc Grady, NC, 28649</div>
                    <div>redq@company.com</div>
                    <div>(128) 666 070</div>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div>
            <div style="margin-bottom: 0.75rem; overflow: auto; ">
            <table style="width: 100%; border-collapse: collapse;">
                    @foreach($categories as $category)
                    <thead>
                        <tr style="border-bottom: 1px solid #ddd; background-color: #f6f8fa;">
                            <th colspan="3" style="font-weight: 800; text-align: center; font-size: 14px; padding: 0.5rem;">{{$category['category']}}</th>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd; background-color: #f6f8fa;">
                            <th style="padding: 0.5rem; text-align: left; font-size: 12px; width: 75%;">Gasto</th>
                            <th style="padding: 0.5rem; text-align: left; font-size: 12px; width: 12.5%;">Fecha</th>
                            <th style="padding: 0.5rem; text-align: center; font-size: 12px; width: 12.5%;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($category['expenses'] as $expense)
                        <tr style="border-bottom: 1px solid #e0e6ed;">
                            <td style="padding: 0.25rem; font-size: 12px;">{{$expense['description']}}</td>
                            <td style="padding: 0.25rem; font-size: 12px;">{{$expense['date']}}</td>
                            <td style="padding: 0.25rem; font-weight: 700; text-align: center; font-size: 12px;">{{$expense['amount']}}$</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @endforeach
                </table>
            </div>
        </div>
        <div style="padding-left: 1rem; padding-right: 1rem; margin-top: 1.5rem;">
            <div style="width: 100%;">
                <div></div>
                <div style="margin-top: 0.5rem; text-align: right;">
                    <div style="margin-bottom: 0.5rem;">
                        <span style="display: inline-block; width: 20%; font-size: 14px; text-align: left;">Subtotal</span>
                        <span style="display: inline-block; width: 10%; text-align: right;">{{$invoice['amount']}}$</span>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span style="display: inline-block; width: 20%; font-size: 14px; text-align: left;">Reserve Fund</span>
                        <span style="display: inline-block; width: 10%; text-align: right;">{{$invoice['reserve_fund']}}$</span>
                    </div>
                    <div style="margin-bottom: 0.5rem; font-weight: 600;">
                        <span style="display: inline-block; width: 20%; font-size: 16px; text-align: left;">Grand Total</span>
                        <span style="display: inline-block; width: 10%; text-align: right;">{{$invoice['total_amount']}}$</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>