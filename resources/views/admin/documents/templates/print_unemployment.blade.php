<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Certificate of Unemployment - {{ $control_no }}</title>
  <style>
    @page {
      margin: 40px 45px;
      size: A4;
    }
    @media print {
      @page { margin: 40px 45px; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12pt;
      line-height: 1.6;
      color: #000;
      margin: 0;
      padding: 0;
      background: white;
    }
    .container {
      max-width: 210mm;
      margin: 0 auto;
      padding: 20px;
    }
    .center { text-align: center; }
    .title { font-size: 16pt; font-weight: bold; margin-top: 18px; }
    .subtitle { font-size: 11pt; margin-top: 4px; color: #555; }
    .hr { border-top: 2px solid #000; margin: 12px 0; }
    .content { margin-top: 20px; }
    .sign { margin-top: 80px; display: flex; justify-content: space-between; }
    .sigblock { width: 45%; text-align: center; }
    .sigline {
      border-top: 1px solid #000;
      margin-top: 50px;
      padding-top: 8px;
      color: #000;
    }
    .no-print {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 9999;
    }
    @media print {
      .no-print { display: none !important; }
    }
  </style>
  <script>
    window.onload = function() {
      setTimeout(function() {
        window.print();
      }, 500);
    };
  </script>
</head>
<body>
  <div class="no-print">
    <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
      🖨️ Print Again
    </button>
  </div>

  <div class="container">
    <div class="center">
      <div><strong>Republic of the Philippines</strong></div>
      <div><strong>Province of Cavite</strong></div>
      <div><strong>City of Imus</strong></div>
      <div><strong>BARANGAY BUCANDALA 1</strong></div>

      <div class="hr"></div>

      <div class="title">BARANGAY CERTIFICATE OF UNEMPLOYMENT</div>
      <div class="subtitle">Control No: {{ $control_no }}</div>
    </div>

    <div class="content">
      <p style="text-align: justify;">
        This is to certify that <strong>{{ $full_name }}</strong>,
        {{ $age }} years old, is
        <strong>
          {{ $is_single }},
          {{ $is_separated }},
          {{ $is_widow }},
          {{ $is_married }}
        </strong>,
        Mr./Mrs. {{ $full_name }}, Filipino, is a bonafide resident of this barangay with postal address at
        <strong>{{ $address }}, Bucandala 1, City of Imus, Cavite</strong> is Unemployed and no source of income.
      </p>

      <p style="text-align: justify;">
        This certification is being issued for {{ $purpose ?? 'his/her necessary requirements' }}
      </p>

      <p style="text-align: justify;">
        Issued this <strong>{{ $day }}</strong> day of <strong>{{ $month }}</strong>,
        <strong>{{ $year }}</strong> at the office of the Sangguniang Barangay of Bucandala 1, City of Imus, Cavite.
      </p>

      <div style="margin-top: 50px; text-align: center;">
        <p style="margin-bottom: 50px;">Received by:</p>
        <p style="margin-top: -30px; margin-bottom: 10px;">
          <strong>{{ $full_name }}</strong>
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
  </div>
</body>
</html>
