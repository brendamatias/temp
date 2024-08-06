<?php

namespace Tests\Feature\Controllers;

use App\Application\DTOs\Invoice\CreateInvoiceDTO;
use App\Application\DTOs\Invoice\UpdateInvoiceDTO;
use App\Application\DTOs\Invoice\ListInvoicesDTO;
use App\Application\DTOs\Invoice\FindInvoiceByIdDTO;
use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\UseCases\Invoice\UpdateInvoiceUseCase;
use App\Application\UseCases\Invoice\ListInvoicesUseCase;
use App\Application\UseCases\Invoice\FindInvoiceByIdUseCase;
use App\Domain\Entities\Invoice;
use App\Infrastructure\Database\Models\User;
use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private CreateInvoiceUseCase $createInvoiceUseCase;
    private UpdateInvoiceUseCase $updateInvoiceUseCase;
    private ListInvoicesUseCase $listInvoicesUseCase;
    private FindInvoiceByIdUseCase $findInvoiceByIdUseCase;
    private User $user;
    private PartnerCompany $partnerCompany;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->createInvoiceUseCase = $this->mock(CreateInvoiceUseCase::class);
        $this->updateInvoiceUseCase = $this->mock(UpdateInvoiceUseCase::class);
        $this->listInvoicesUseCase = $this->mock(ListInvoicesUseCase::class);
        $this->findInvoiceByIdUseCase = $this->mock(FindInvoiceByIdUseCase::class);

        $this->user = User::factory()->create();
        $this->partnerCompany = PartnerCompany::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_create_invoice(): void
    {
        $this->createInvoiceUseCase
            ->shouldReceive('execute')
            ->once()
            ->withArgs(function (CreateInvoiceDTO $dto) {
                return $dto->number === '123456'
                    && $dto->partnerCompanyId === $this->partnerCompany->id
                    && $dto->value === 1000.00;
            });

        $response = $this->postJson('/api/invoices', [
            'number' => '123456',
            'partnerCompanyId' => $this->partnerCompany->id,
            'value' => 1000.00,
            'service_description' => 'Serviço de desenvolvimento',
            'competence_month' => '2024-03-01',
            'receipt_date' => '2024-03-20'
        ]);

        $response->assertStatus(201);
    }

    public function test_can_update_invoice(): void
    {
        $this->updateInvoiceUseCase
            ->shouldReceive('execute')
            ->once()
            ->withArgs(function (UpdateInvoiceDTO $dto) {
                return $dto->id === 1
                    && $dto->number === '654321'
                    && $dto->value === 2000.00;
            });

        $response = $this->putJson('/api/invoices/1', [
            'number' => '654321',
            'value' => 2000.00,
            'service_description' => 'Serviço atualizado'
        ]);

        $response->assertStatus(200);
    }

    public function test_can_list_invoices(): void
    {
        $invoices = [
            ['id' => 1, 'number' => 'NF001'],
            ['id' => 2, 'number' => 'NF002'],
            ['id' => 3, 'number' => 'NF003']
        ];
        
        $this->listInvoicesUseCase
            ->shouldReceive('execute')
            ->once()
            ->withArgs(function (ListInvoicesDTO $dto) {
                return $dto->includeInactive === false;
            })
            ->andReturn($invoices);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_find_invoice_by_id(): void
    {
        $invoice = ['id' => 1, 'number' => 'NF001'];
        
        $this->findInvoiceByIdUseCase
            ->shouldReceive('execute')
            ->once()
            ->withArgs(function (FindInvoiceByIdDTO $dto) {
                return $dto->id === 1;
            })
            ->andReturn($invoice);

        $response = $this->getJson('/api/invoices/1');

        $response->assertStatus(200)
            ->assertJson($invoice);
    }

    public function test_returns_404_when_invoice_not_found(): void
    {
        $this->findInvoiceByIdUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn(null);

        $response = $this->getJson('/api/invoices/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Nota fiscal não encontrada']);
    }

    public function test_validates_required_fields_on_create(): void
    {
        $response = $this->postJson('/api/invoices', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['number', 'partnerCompanyId', 'value', 'service_description', 'competence_month', 'receipt_date']);
    }

    public function test_validates_date_format_on_create(): void
    {
        $response = $this->postJson('/api/invoices', [
            'number' => '123456',
            'partnerCompanyId' => $this->partnerCompany->id,
            'value' => 1000.00,
            'service_description' => 'Serviço de desenvolvimento',
            'competence_month' => 'invalid-date',
            'receipt_date' => 'invalid-date'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['competence_month', 'receipt_date']);
    }
} 