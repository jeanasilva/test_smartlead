<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskUpdated extends Notification
{
    use Queueable;

    protected $task;
    protected $changes;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, array $changes = [])
    {
        $this->task = $task;
        $this->changes = $changes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Tarefa Atualizada - ' . $this->task->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Uma tarefa foi atualizada.')
            ->line('**Título:** ' . $this->task->title)
            ->line('**Descrição:** ' . ($this->task->description ?: 'Sem descrição'))
            ->line('**Status:** ' . $this->getStatusLabel($this->task->status))
            ->line('**Prioridade:** ' . $this->getPriorityLabel($this->task->priority));

        if ($this->task->due_date) {
            $message->line('**Prazo:** ' . $this->task->due_date->format('d/m/Y'));
        }

        if (!empty($this->changes)) {
            $message->line('**Alterações realizadas:**');
            foreach ($this->changes as $field => $change) {
                $message->line('• ' . $this->getFieldLabel($field) . ': ' . $change['from'] . ' → ' . $change['to']);
            }
        }

        $message->line('**Empresa:** ' . $this->task->company->name)
            ->action('Ver Tarefa', url('/tasks/' . $this->task->id))
            ->line('Obrigado por usar nosso sistema!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'changes' => $this->changes,
            'company_name' => $this->task->company->name,
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída'
        ];
        return $labels[$status] ?? $status;
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta'
        ];
        return $labels[$priority] ?? $priority;
    }

    private function getFieldLabel($field)
    {
        $labels = [
            'title' => 'Título',
            'description' => 'Descrição',
            'status' => 'Status',
            'priority' => 'Prioridade',
            'due_date' => 'Prazo',
            'user_id' => 'Responsável'
        ];
        return $labels[$field] ?? $field;
    }
}
