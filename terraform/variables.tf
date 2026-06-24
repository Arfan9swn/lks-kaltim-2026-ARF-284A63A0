# variables.tf
variable "region" {
  description = "AWS region"
  type        = string
  default     = "ap-southeast-3"
}

variable "project_name" {
  description = "Project name used for resource naming"
  type        = string
  default     = "lks-cc-2026"
}

variable "environment" {
  description = "Environment (dev, staging, prod)"
  type        = string
  default     = "dev"
}

variable "common_tags" {
  description = "Common tags applied to all resources"
  type        = map(string)
  default = {
    Environment = "dev"
    ManagedBy   = "Terraform"
    Project     = "lks-cc-2026"
  }
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "availability_zones" {
  description = "Availability zones for subnets"
  type        = list(string)
  default     = ["ap-southeast-3a", "ap-southeast-3b"]
}

variable "public_subnet_cidrs" {
  description = "CIDR blocks for public subnets"
  type        = list(string)
  default     = ["10.0.1.0/24", "10.0.2.0/24"]
}

variable "private_app_subnet_cidrs" {
  description = "CIDR blocks for private app subnets"
  type        = list(string)
  default     = ["10.0.10.0/24", "10.0.20.0/24"]
}

variable "private_db_subnet_cidrs" {
  description = "CIDR blocks for private database subnets"
  type        = list(string)
  default     = ["10.0.30.0/24", "10.0.40.0/24"]
}

variable "db_engine" {
  description = "Database engine (mysql or postgres)"
  type        = string
  default     = "mysql"
  validation {
    condition     = contains(["mysql", "postgres"], var.db_engine)
    error_message = "DB engine must be either 'mysql' or 'postgres'."
  }
}

variable "db_engine_version" {
  description = "Database engine version"
  type        = string
  default     = "8.0" #mysql 8
}

variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.micro"
}

variable "db_name" {
  description = "Name of the database"
  type        = string
  default     = "appdb"
  validation {
    condition     = can(regex("^[a-zA-Z][a-zA-Z0-9]*$", var.db_name))
    error_message = "DB name must begin with a letter and contain only alphanumeric characters."
  }
}

variable "db_username" {
  description = "Master username for RDS"
  type        = string
  default     = "admin"
  sensitive   = true
}

variable "db_password" {
  description = "Master password for RDS"
  type        = string
  default     = "761244123"
  sensitive   = true
}

variable "s3_bucket_name" {
  description = "Name of the S3 bucket for file uploads"
  type        = string
  default     = "my-project-uploads-bucket-12345" #HARUS UNIK
}