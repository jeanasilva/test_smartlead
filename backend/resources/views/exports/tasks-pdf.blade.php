<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Tarefas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #6b7280;
            margin: 5px 0;
            font-size: 14px;
        }
        
        .info-section {
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .stats-section {
            margin-bottom: 25px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .tasks-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        
        .tasks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .tasks-table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .tasks-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        
        .tasks-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pendente {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-em-andamento {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-concluida {
            background: #d1fae5;
            color: #065f46;
        }
        
        .priority-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .priority-alta {
            background: #fecaca;
            color: #991b1b;
        }
        
        .priority-media {
            background: #fed7aa;
            color: #9a3412;
        }
        
        .priority-baixa {
            background: #bbf7d0;
            color: #166534;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Tarefas</h1>
        <div class="subtitle">{{ $company }}</div>
        <div class="subtitle">Gerado em: {{ $generated_at }}</div>
    </div>

    <div class="info-section">
        <h3 style="margin-top: 0;">Informações do Relatório</h3>
        <div class="info-row">
            <span><strong>Empresa:</strong></span>
            <span>{{ $company }}</span>
        </div>
        @if(isset($filters['date_from']) && $filters['date_from'])
        <div class="info-row">
            <span><strong>Período:</strong></span>
            <span>{{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} até {{ isset($filters['date_to']) && $filters['date_to'] ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : 'Presente' }}</span>
        </div>
        @endif
        @if(isset($filters['status']) && $filters['status'])
        <div class="info-row">
            <span><strong>Status:</strong></span>
            <span>{{ $statusLabels[$filters['status']] ?? $filters['status'] }}</span>
        </div>
        @endif
        @if(isset($filters['priority']) && $filters['priority'])
        <div class="info-row">
            <span><strong>Prioridade:</strong></span>
            <span>{{ $priorityLabels[$filters['priority']] ?? $filters['priority'] }}</span>
        </div>
        @endif
    </div>

    <div class="stats-section">
        <h3 class="section-title">Resumo Estatístico</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Total de Tarefas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['completed'] }}</div>
                <div class="stat-label">Concluídas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['in_progress'] }}</div>
                <div class="stat-label">Em Andamento</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pendentes</div>
            </div>
        </div>
    </div>

    @if($tasks->count() > 0)
    <div class="tasks-section">
        <h3 class="section-title">Lista de Tarefas</h3>
        <table class="tasks-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Prioridade</th>
                    <th>Responsável</th>
                    <th>Vencimento</th>
                    <th>Criada em</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ Str::limit($task->title, 30) }}</td>
                    <td>
                        <span class="status-badge status-{{ $task->status }}">
                            {{ $statusLabels[$task->status] ?? $task->status }}
                        </span>
                    </td>
                    <td>
                        <span class="priority-badge priority-{{ $task->priority }}">
                            {{ $priorityLabels[$task->priority] ?? $task->priority }}
                        </span>
                    </td>
                    <td>{{ $task->user->name }}</td>
                    <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $task->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="tasks-section">
        <h3 class="section-title">Lista de Tarefas</h3>
        <p style="text-align: center; color: #6b7280; font-style: italic;">
            Nenhuma tarefa encontrada com os filtros aplicados.
        </p>
    </div>
    @endif

    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema de Gestão de Tarefas em {{ $generated_at }}</p>
        <p>Total de {{ $tasks->count() }} {{ $tasks->count() == 1 ? 'tarefa encontrada' : 'tarefas encontradas' }}</p>
    </div>
</body>
</html>
