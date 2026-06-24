# outputs.tf

output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.main.id
}

output "vpc_cidr" {
  description = "CIDR block of the VPC"
  value       = aws_vpc.main.cidr_block
}

#subnet
output "public_subnet_ids" {
  description = "IDs of public subnets"
  value       = aws_subnet.public[*].id
}

output "public_subnet_cidrs" {
  description = "CIDR blocks of public subnets"
  value       = aws_subnet.public[*].cidr_block
}

output "private_app_subnet_ids" {
  description = "IDs of private app subnets"
  value       = aws_subnet.private_app[*].id
}

output "private_app_subnet_cidrs" {
  description = "CIDR blocks of private app subnets"
  value       = aws_subnet.private_app[*].cidr_block
}

output "private_db_subnet_ids" {
  description = "IDs of private database subnets"
  value       = aws_subnet.private_db[*].id
}

output "private_db_subnet_cidrs" {
  description = "CIDR blocks of private database subnets"
  value       = aws_subnet.private_db[*].cidr_block
}

#secgroup
output "alb_security_group_id" {
  description = "ID of ALB security group"
  value       = aws_security_group.alb.id
}

output "app_security_group_id" {
  description = "ID of app security group"
  value       = aws_security_group.app.id
}

output "rds_security_group_id" {
  description = "ID of RDS security group"
  value       = aws_security_group.rds.id
}

output "elasticache_security_group_id" {
  description = "ID of ElastiCache security group"
  value       = aws_security_group.elasticache.id
}

#rds output
output "rds_endpoint" {
  description = "Endpoint of RDS instance"
  value       = aws_db_instance.main.endpoint
}

output "rds_address" {
  description = "Address of RDS instance"
  value       = aws_db_instance.main.address
}

output "rds_port" {
  description = "Port of RDS instance"
  value       = aws_db_instance.main.port
}

output "rds_db_name" {
  description = "Database name"
  value       = aws_db_instance.main.db_name
}

output "rds_username" {
  description = "Database master username"
  value       = aws_db_instance.main.username
  sensitive   = true
}

#s3 output
output "s3_bucket_name" {
  description = "Name of the S3 bucket"
  value       = aws_s3_bucket.uploads.id
}

output "s3_bucket_arn" {
  description = "ARN of the S3 bucket"
  value       = aws_s3_bucket.uploads.arn
}

#ngw output
output "nat_gateway_id" {
  description = "ID of NAT Gateway"
  value       = aws_nat_gateway.main.id
}

output "nat_gateway_public_ip" {
  description = "Public IP of NAT Gateway"
  value       = aws_eip.nat.public_ip
}