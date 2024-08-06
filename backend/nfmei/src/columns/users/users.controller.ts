import { Controller, Post, Body, Patch, UseGuards, Param} from '@nestjs/common';
import { UsersService } from './users.service';
import { CreateUserDto } from './dto/create-user.dto';
import { ApiOkResponse } from '@nestjs/swagger';
import { plainToInstance } from 'class-transformer';
import { UpdateUserDto } from './dto/update-user.dto';
import { UserResponseDto } from './dto/user-response.dto';
import { JwtAuthGuard } from 'src/common/guards/jwt-auth.guard';

@Controller('users')
export class UsersController {
  constructor(private readonly usersService: UsersService) {}

  @Post('register')
  create(@Body() createUserDto: CreateUserDto) {
    return this.usersService.create(createUserDto);
  }

  @UseGuards(JwtAuthGuard)
  @Patch(':id')
  @ApiOkResponse({ type: UserResponseDto })
  async update(@Param('id') id: string, @Body() dto: UpdateUserDto): Promise<UserResponseDto> {
    const user = await this.usersService.update(id, dto);
    return plainToInstance(UserResponseDto, user, { excludeExtraneousValues: true });
  }
}
