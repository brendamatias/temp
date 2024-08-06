import { ConflictException, Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { User } from './entities/user.entity';
import { Repository } from 'typeorm';

import * as bcrypt from 'bcryptjs';
import { CreateUserDto } from './dto/create-user.dto';
import { UpdateUserDto } from './dto/update-user.dto';

@Injectable()
export class UsersService {
  constructor(@InjectRepository(User) private readonly repo: Repository<User>) {}

  async findByEmail(email: string): Promise<User | null> {
    return this.repo.findOne({ where: { email } });
  }

  async create(createUserDto: CreateUserDto): Promise<User> {
    const { password, email, role, fullName, phone } = createUserDto;
    
    const passwordHash = await bcrypt.hash(password, 10);
    const user = this.repo.create({ 
      email, 
      password: passwordHash, 
      fullName,
      phone,
      role, 
      isActive: true 
    });

    try {
      const saved = await this.repo.save(user);
      const { password, ...rest } = saved;
      return rest as User;
    } catch (error) {
      if(error?.code === '23505') {
        const detail: string = error.detail || '';
        if (detail.includes('(email)')) {
          throw new ConflictException('Email already in use');
        }
        if (detail.includes('(phone)')) {
          throw new ConflictException('Phone already in use');
        }
        throw new ConflictException('Unique constraint violation');
      }

      throw error;
    }

  }


    async update(id: string, updateUserDto: UpdateUserDto): Promise<User> {
      const { password, email, role, fullName, phone, isActive } = updateUserDto;

      const user = await this.repo.findOne({ where: { id } });
      if (!user) throw new NotFoundException('User not found');

      if (email !== undefined) user.email = email.trim().toLowerCase();
      if (fullName !== undefined) user.fullName = fullName?.trim() || null;
      if (phone !== undefined) user.phone = phone?.trim() || null;
      if (role !== undefined) user.role = role;
      if (isActive !== undefined) user.isActive = isActive;

      if (password) {
        user.password = await bcrypt.hash(password, 10);
      }

      try {
        const saved = await this.repo.save(user);
        const { password, ...rest } = saved as any;
        return rest as User;
      } catch (error: any) {
        if (error?.code === '23505') {
          const d: string = error.detail || '';
          if (d.includes('(email)')) throw new ConflictException('You cant use this email');
          if (d.includes('(phone)')) throw new ConflictException('You cant use this phone');
          throw new ConflictException('Unique constraint violation');
        }
        throw error;
      }
  }

  strip(user: User) {
    const { password, ...rest } = user;
    return rest;
  }
}
