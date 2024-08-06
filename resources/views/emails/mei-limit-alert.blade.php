<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alerta de Limite do MEI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .highlight {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Alerta de Limite do MEI</h2>
        
        <div class="alert">
            <p>Olá,</p>
            <p>Este é um alerta importante sobre o limite anual do seu MEI.</p>
            <p>Você atingiu <span class="highlight">{{ number_format($percentage, 2) }}%</span> do limite anual do MEI.</p>
            <p>Valor atual: R$ {{ number_format($currentAmount, 2, ',', '.') }}</p>
            <p>Limite anual: R$ {{ number_format($limit, 2, ',', '.') }}</p>
        </div>

        <p>Recomendamos que você:</p>
        <ul>
            <li>Monitore seus lançamentos com mais atenção</li>
            <li>Considere distribuir melhor seus recebimentos ao longo do ano</li>
            <li>Verifique se há possibilidade de adiar alguns recebimentos para o próximo ano</li>
        </ul>

        <p>Atenciosamente,<br>Equipe MEI Organizer</p>
    </div>
</body>
</html> 