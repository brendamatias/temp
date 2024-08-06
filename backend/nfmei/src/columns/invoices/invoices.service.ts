import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Invoice } from './entities/invoice.entity';
import { CreateInvoiceDto } from './dto/create-invoice.dto';
import { UpdateInvoiceDto } from './dto/update-invoice.dto';

@Injectable()
export class InvoicesService {
  constructor(
    @InjectRepository(Invoice)
    private readonly repo: Repository<Invoice>,
  ) {}

  async create(dto: CreateInvoiceDto): Promise<Invoice> {
    const entity = this.repo.create(dto);
    return this.repo.save(entity);
  }

  async findAll(): Promise<Invoice[]> {
    return this.repo.find({ relations: ['partner'] });
  }

  async findOne(id: string): Promise<Invoice> {
    const entity = await this.repo.findOne({ where: { id }, relations: ['partner'] });
    if (!entity) throw new NotFoundException(`Invoice with ID "${id}" not found`);
    return entity;
  }

  async update(id: string, dto: UpdateInvoiceDto): Promise<Invoice> {
    const preload = await this.repo.preload({ id, ...dto });
    if (!preload) throw new NotFoundException(`Invoice with ID "${id}" not found`);
    return this.repo.save(preload);
  }

  async remove(id: string): Promise<void> {
    const entity = await this.findOne(id);
    await this.repo.remove(entity);
  }

  async findByPartner(partnerId: string): Promise<Invoice[]> {
    return this.repo.find({ where: { partnerId }, relations: ['partner'] });
  }
}