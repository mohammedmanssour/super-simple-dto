<?php

namespace MohammedManssour\DTO\Tests\Concerns;

use Mockery;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use MohammedManssour\DTO\Tests\Stubs\User;
use MohammedManssour\DTO\Tests\Stubs\UserData;
use MohammedManssour\DTO\Tests\Stubs\BalanceData;
use MohammedManssour\DTO\Tests\Stubs\FormRequest;
use MohammedManssour\DTO\Tests\Stubs\Status;

class AsDTOTest extends TestCase
{
    private array $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'name' => 'Mohammed Manssour',
            'email' => 'hello@mohammedmanssour.me',
            'status' => 'active',
            'balance' => [
                'bitcoin' => 10.01,
                'usdollar' => 1000
            ],
        ];
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromCollection
     * */
    public function it_converts_collection_to_dto()
    {
        $dto = UserData::fromCollection(collect($this->data));

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromArray
     * */
    public function it_converts_array_to_dto()
    {
        $dto = UserData::fromArray($this->data);

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromRequest
     * */
    public function it_converts_request_to_dto_with_using_all_method()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($this->data);

        $dto = UserData::fromRequest(request: $request, useAll: true);

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromRequest
     * */
    public function it_converts_request_to_dto_with_using_all_method_because_validated_method_does_not_exists()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($this->data);

        $dto = UserData::fromRequest($request);

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromRequest
     * */
    public function it_converts_request_to_dto_without_using_all_method()
    {
        $request = Mockery::mock(FormRequest::class);
        $request->shouldNotReceive('all');
        $request->shouldReceive('validated')->andReturn($this->data);

        $dto = UserData::fromRequest($request);

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromModel
     * */
    public function it_converts_model_to_dto()
    {
        $model = (new User())->forceFill($this->data);
        $dto = UserData::fromModel($model);

        $this->assertDTO($dto);
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::toArray
     * */
    public function it_converts_dto_to_array()
    {
        $dto = UserData::fromArray($this->data);

        $this->assertEquals($this->data, $dto->toArray());
    }

    /**
     * @test
     * @covers \MohammedManssour\DTO\Concerns\AsDTO::fromCollection
     * */
    public function it_only_handles_available_data()
    {
        $data = [
            'name' => 'Mohammed Manssour'
        ];
        $dto = UserData::fromArray($data);

        $this->assertEquals($data['name'], $dto->name);
        $this->assertFalse(isset($dto->email));
        $this->assertFalse(isset($dto->balance));
        $this->assertFalse(isset($dto->status));

        $this->assertEquals($data, $dto->toArray());
    }

    private function assertDTO(UserData $dto)
    {
        $this->assertEquals($this->data['name'], $dto->name);
        $this->assertEquals($this->data['email'], $dto->email);

        $this->assertInstanceOf(BalanceData::class, $dto->balance);
        $this->assertEquals($this->data['balance']['bitcoin'], $dto->balance->bitcoin);
        $this->assertEquals($this->data['balance']['usdollar'], $dto->balance->usdollar);

        $this->assertInstanceOf(Status::class, $dto->status);
        $this->assertEquals(Status::Active, $dto->status);
    }
}
