import { Injectable, NotFoundException } from '@nestjs/common';
import { CreatePartnerDto } from './dto/create-partner.dto';
import { UpdatePartnerDto } from './dto/update-partner.dto';
import { Partner } from './entities/partner.entity';
import { Repository } from 'typeorm';
import { InjectRepository } from '@nestjs/typeorm';

@Injectable()
export class PartnerService {
  constructor(
    @InjectRepository(Partner)
    private readonly repo: Repository<Partner>,
  ) {}

  async create(dto: CreatePartnerDto): Promise<Partner> {
    const entity = this.repo.create(dto);
    return this.repo.save(entity);
  }

  async findAll(): Promise<Partner[]> {
    return this.repo.find();
  }

  async findOne(id: string): Promise<Partner> {
    const entity = await this.repo.findOne({ where: { id } });
    if (!entity) throw new NotFoundException(`Partner with ID "${id}" not found`);
    return entity;
  }

  async update(id: string, dto: UpdatePartnerDto): Promise<Partner> {
    const preload = await this.repo.preload({ id, ...dto });
    if (!preload) throw new NotFoundException(`Partner with ID "${id}" not found`);
    return this.repo.save(preload);
  }

  async remove(id: string): Promise<void> {
    const entity = await this.findOne(id);
    await this.repo.remove(entity);
  }
}
