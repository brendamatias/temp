import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Setting } from './entities/setting.entity';
import { CreateSettingDto } from './dto/create-setting.dto';
import { UpdateSettingDto } from './dto/update-setting.dto';

@Injectable()
export class SettingsService {
  constructor(
    @InjectRepository(Setting)
    private readonly settingsRepository: Repository<Setting>,
  ) {}


  async create(createSettingDto: CreateSettingDto): Promise<Setting[]> {
    const setting = this.settingsRepository.create(createSettingDto as any);
    return this.settingsRepository.save(setting);
  }

  async findAll(): Promise<Setting[]> {
    return this.settingsRepository.find({
      relations: ['user'], 
    });
  }

  async findOne(id: number): Promise<Setting> {
    const setting = await this.settingsRepository.findOne({
      where: { id: String(id) as any },
      relations: ['user'],
    });

    if (!setting) throw new NotFoundException(`Settings with ID "${id}" not found`);
    return setting;
  }

  async update(id: number, updateSettingDto: UpdateSettingDto): Promise<Setting> {
    const preload = await this.settingsRepository.preload({
      id: String(id) as any,
      ...(updateSettingDto as any),
    });

    if (!preload) throw new NotFoundException(`Settings with ID "${id}" not found`);
    return this.settingsRepository.save(preload);
  }

  async remove(id: number): Promise<void> {
    const setting = await this.findOne(id);
    await this.settingsRepository.remove(setting);
  }

  async findByUserId(userId: string): Promise<Setting> {
    const setting = await this.settingsRepository.findOne({
      where: { userId },
      relations: ['user'],
    });

    console.log(setting)
    if (!setting) throw new NotFoundException(`Settings for userId "${userId}" not found`);
    return setting;
  }

  async upsertByUserId(userId: string, dto: UpdateSettingDto): Promise<Setting | Setting[]> {
    const existing = await this.settingsRepository.findOne({ where: { userId } });

    if (existing) {
      const merged = this.settingsRepository.merge(existing, dto as any);
      return this.settingsRepository.save(merged);
    }

    const created = this.settingsRepository.create({ ...(dto as any), userId });
    return this.settingsRepository.save(created);
  }

}