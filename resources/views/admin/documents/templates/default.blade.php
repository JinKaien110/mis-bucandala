<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 40px 45px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.8; }
    .center { text-align: center; }
    .title { font-size: 16px; font-weight: bold; margin-top: 18px; }
    .subtitle { font-size: 12px; margin-top: 4px; }
    .hr { border-top: 1px solid #000; margin: 12px 0; }
    .content { margin-top: 18px; }
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
    <p style="text-align: justify;">
      This is to certify that <strong>{{ $full_name ?? '' }}</strong>,
      {{ $age ?? '' }} years old, is
      <strong>
        @if($is_single) Single(✓) @else Single( ) @endif,
        @if($is_separated) Separated(✓) @else Separated( ) @endif,
        @if($is_widow) Widow/Widower(✓) @else Widow/Widower( ) @endif,
        @if($is_married) Married(✓) @else Married( ) @endif
      </strong>
      Mr./Mrs. {{ $full_name ?? '' }}, Filipino, is a bonafide resident of this barangay with postal address at
      <strong>{{ $address ?? '' }}, Bucandala 1, City of Imus, Cavite.</strong>
    </p>

    <p style="text-align: justify;">
      He/she is personally known to me be a law-abiding citizen and has a good moral character. Record of this barangay has shown that he/she has not committed nor been involved in any kind of unlawful activities in this barangay.
    </p>

    <p style="text-align: justify;">
      Certification issued upon the request of the above mentioned person for <strong>{{ $purpose ?? 'N/A' }}</strong>
    </p>

    <p style="text-align: justify;">
      Issued this <strong>{{ $day ?? date('d') }}</strong> day of
      <strong>{{ $month ?? date('F') }}</strong>,
      <strong>{{ $year ?? date('Y') }}</strong>, at the office of the Sangguniang Barangay of the Bucandala I, City of Imus, Cavite.
    </p>

    <div style="margin-top: 50px; text-align: center;">
      <p>Received by:</p>
      <p style="margin-top: 30px;">
        <strong>{{ $full_name ?? '' }}</strong>
      </p>
      <p>Signature of Applicant or Right Thumb Mark</p>
    </div>
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
