# rds.tf

resource "aws_db_subnet_group" "main" {
  name        = "${var.project_name}-db-subnet-group"
  description = "Subnet group for RDS database"
  subnet_ids  = aws_subnet.private_db[*].id

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-db-subnet-group"
  })
}

resource "aws_db_parameter_group" "main" {
  family = var.db_engine == "mysql" ? "mysql8.0" : "postgres15"

  parameter {
    name  = var.db_engine == "mysql" ? "character_set_server" : "client_encoding"
    value = "utf8"
  }

  parameter {
    name  = var.db_engine == "mysql" ? "collation_server" : "server_encoding"
    value = "utf8_general_ci"
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-db-param-group"
  })
}

resource "aws_db_instance" "main" {
  identifier = "${var.project_name}-database"

  engine         = var.db_engine
  engine_version = var.db_engine_version

  instance_class    = var.db_instance_class
  allocated_storage = 20
  storage_type      = "gp2"
  storage_encrypted = true

  db_name  = var.db_name
  username = var.db_username
  password = var.db_password

  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = [aws_security_group.rds.id]

  multi_az = false

  #backup
  backup_retention_period = 7
  backup_window           = "03:00-04:00"
  maintenance_window      = "Mon:04:00-Mon:05:00"

  performance_insights_enabled = false

  deletion_protection = false # Set to true in production

  skip_final_snapshot = true

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-rds"
  })

  lifecycle {
    prevent_destroy = false
  }
}