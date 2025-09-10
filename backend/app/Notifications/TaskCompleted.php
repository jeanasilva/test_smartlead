<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskCompleted extends Notification
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
        return (new MailMessage)
                    ->subject('Tarefa Concluída - ' . $this->task->title)
                    ->greeting('Olá ' . $notifiable->name . '!')
                    ->line('Parabéns! Uma tarefa foi concluída.')
                    ->line('**Título:** ' . $this->task->title)
                    ->line('**Descrição:** ' . ($this->task->description ?: 'Sem descrição'))
                    ->line('**Prioridade:** ' . ucfirst($this->task->priority))
                    ->line('**Concluída por:** ' . $this->task->user->name)
                    ->when($this->task->due_date, function ($message) {
                        return $message->line('**Prazo:** ' . $this->task->due_date->format('d/m/Y'));
                    })
                    ->line('**Empresa:** ' . $this->task->company->name)
                    ->action('Ver Tarefa', url('/tasks/' . $this->task->id))
                    ->line('Continue o ótimo trabalho!');
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
            'completed_by' => $this->task->user->name,
            'company_name' => $this->task->company->name,
        ];
    }
}
