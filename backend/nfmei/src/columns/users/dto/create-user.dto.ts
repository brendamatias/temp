import { ApiProperty } from "@nestjs/swagger";
import { IsBoolean, IsEmail, IsOptional, IsString, Matches, MaxLength, MinLength } from "class-validator";

export class CreateUserDto {
  @ApiProperty({ example: 'admin@local.dev' })
  @IsEmail()
  email!: string;

  @ApiProperty({ minLength: 6, example: 'admin123' })
  @IsString()
  @MinLength(6)
  password!: string; 

  @ApiProperty({ example: 'Admin Local' })
  @IsOptional()
  @IsString()
  @MaxLength(255)
  fullName?: string;

  @ApiProperty({ example: '+5511987654321' })
  @IsOptional()
  @IsString()
  @Matches(/^[0-9+\-\s().]{8,20}$/, { message: 'phone must be a valid phone-like string' })
  phone?: string;

  @ApiProperty({ example: 'user', required: false })
  @IsOptional()
  @IsString()
  role!: string;

  @ApiProperty({ example: true, default: true })
  @IsOptional()
  @IsBoolean()
  isActive?: boolean = true;
}
