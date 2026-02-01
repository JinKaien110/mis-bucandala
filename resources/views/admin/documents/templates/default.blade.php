<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 40px 45px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .center { text-align: center; }
    .title { font-size: 16px; font-weight: bold; margin-top: 18px; }
    .subtitle { font-size: 12px; margin-top: 4px; }
    .hr { border-top: 1px solid #000; margin: 12px 0; }
    .content { margin-top: 18px; line-height: 1.8; }
    .sign { margin-top: 60px; display: flex; justify-content: space-between; }
    .sigblock { width: 45%; text-align: center; }
    .sigline { border-top: 1px solid #000; margin-top: 50px; }
    .small { font-size: 11px; }
  </style>
</head>
<body>

  <div class="center">
    <div><strong>Republic of the Philippines</strong></div>
    <div><strong>Province of Cavite</strong></div>
    <div><strong>City of Imus</strong></div>
    <div><strong>BARANGAY BUCANDALA 1</strong></div>

    <div class="hr"></div>

    <div class="title">{{ strtoupper($req->documentType->name ?? 'BARANGAY DOCUMENT') }}</div>
    <div class="subtitle">Control No: {{ $req->control_no }}</div>
  </div>

  <div class="content">
    <p>
      This is to certify that <strong>
        {{ $req->resident->first_name }}
        {{ $req->resident->middle_name ? $req->resident->middle_name.' ' : '' }}
        {{ $req->resident->last_name }}
      </strong>
      is a resident of <strong>{{ $req->resident->address_line }}</strong>.
    </p>

    @if(!empty($req->purpose))
      <p><strong>Purpose:</strong> {{ $req->purpose }}</p>
    @endif

    <p>
      Issued this <strong>{{ optional($req->released_at ?? $req->created_at)->format('F d, Y') }}</strong>
      at Barangay Bucandala 1, Imus, Cavite.
    </p>

    <p class="small">
      Fee: ₱{{ number_format($req->fee_amount ?? 0, 2) }}
    </p>
  </div>

  <div class="sign">
    <div class="sigblock">
      <div class="sigline"></div>
      <div><strong>Barangay Secretary</strong></div>
    </div>

    <div class="sigblock">
      <div class="sigline"></div>
      <div><strong>Barangay Captain</strong></div>
    </div>
  </div>

</body>
</html>
