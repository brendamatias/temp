import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { UsersService } from './users.service';
import { User } from './entities/user.entity';
import { DataSource } from 'typeorm';          
import { JwtService } from '@nestjs/jwt';     
import { ConfigService } from '@nestjs/config'; 

const makeRepoMock = () => ({});

describe('UsersService', () => {
  let service: UsersService;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        UsersService,
        { provide: getRepositoryToken(User), useValue: makeRepoMock() },
        { provide: DataSource, useValue: { query: jest.fn(), manager: {}, createQueryRunner: jest.fn() } },
        { provide: JwtService, useValue: { sign: jest.fn(), signAsync: jest.fn(), verify: jest.fn(), verifyAsync: jest.fn() } },
        { provide: ConfigService, useValue: { get: jest.fn() } },
      ],
    }).compile();

    service = module.get<UsersService>(UsersService);
    jest.clearAllMocks();
  });

  it('should be defined', () => {
    expect(service).toBeDefined();
  });
});
