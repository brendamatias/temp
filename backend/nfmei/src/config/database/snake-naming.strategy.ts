import { snakeCase } from 'src/utils/snake-case.utils';
import { DefaultNamingStrategy, NamingStrategyInterface } from 'typeorm';


export class SnakeNamingStrategy
  extends DefaultNamingStrategy
  implements NamingStrategyInterface
{
  tableName(className: string, customName?: string): string {
    return customName ? snakeCase(customName) : snakeCase(className);
  }
  columnName(propertyName: string, customName: string, embeddedPrefixes: string[]): string {
    const prefix = embeddedPrefixes.join('_');
    return snakeCase(prefix + (customName ?? propertyName));
  }
  relationName(propertyName: string): string {
    return snakeCase(propertyName);
  }
  joinColumnName(relationName: string, referencedColumnName: string): string {
    return snakeCase(`${relationName}_${referencedColumnName}`);
  }
  joinTableName(firstTableName: string, secondTableName: string, firstPropertyName: string): string {
    return snakeCase(`${firstTableName}_${firstPropertyName}_${secondTableName}`);
  }
  joinTableColumnName(tableName: string, propertyName: string, columnName?: string): string {
    return snakeCase(`${tableName}_${columnName ?? propertyName}`);
  }
  classTableInheritanceParentColumnName(parentTableName: string, parentTableIdPropertyName: string): string {
    return snakeCase(`${parentTableName}_${parentTableIdPropertyName}`);
  }
  eagerJoinRelationAlias(alias: string, propertyPath: string): string {
    return snakeCase(`${alias}__${propertyPath.replace('.', '__')}`);
  }
}