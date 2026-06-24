# vpc.tf

# Create VPC
resource "aws_vpc" "main" {
  cidr_block           = var.vpc_cidr
  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-vpc"
  })
}

#igw
resource "aws_internet_gateway" "main" {
  vpc_id = aws_vpc.main.id

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-igw"
  })
}

#2 dif az pub subnets
resource "aws_subnet" "public" {
  count             = length(var.public_subnet_cidrs)
  vpc_id            = aws_vpc.main.id
  cidr_block        = var.public_subnet_cidrs[count.index]
  availability_zone = var.availability_zones[count.index]

  map_public_ip_on_launch = true

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-public-subnet-${count.index + 1}"
    Tier = "Public"
  })
}

#2 dif az pri subnets
resource "aws_subnet" "private_app" {
  count             = length(var.private_app_subnet_cidrs)
  vpc_id            = aws_vpc.main.id
  cidr_block        = var.private_app_subnet_cidrs[count.index]
  availability_zone = var.availability_zones[count.index]

  map_public_ip_on_launch = false

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-private-app-subnet-${count.index + 1}"
    Tier = "Private-App"
  })
}

#2 dif az pri db subnets
resource "aws_subnet" "private_db" {
  count             = length(var.private_db_subnet_cidrs)
  vpc_id            = aws_vpc.main.id
  cidr_block        = var.private_db_subnet_cidrs[count.index]
  availability_zone = var.availability_zones[count.index]

  map_public_ip_on_launch = false

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-private-db-subnet-${count.index + 1}"
    Tier = "Private-DB"
  })
}

#eip for ngw
resource "aws_eip" "nat" {
  domain = "vpc"

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-nat-eip"
  })
}

#ngw pub subnet 1
resource "aws_nat_gateway" "main" {
  allocation_id = aws_eip.nat.id
  subnet_id     = aws_subnet.public[0].id

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-nat-gateway"
  })

  depends_on = [aws_internet_gateway.main]
}

#pub rtb
resource "aws_route_table" "public" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.main.id
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-public-rt"
  })
}

#private app rtb
resource "aws_route_table" "private_app" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block     = "0.0.0.0/0"
    nat_gateway_id = aws_nat_gateway.main.id
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-private-app-rt"
  })
}

#private db rtb
resource "aws_route_table" "private_db" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block     = "0.0.0.0/0"
    nat_gateway_id = aws_nat_gateway.main.id
  }

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-private-db-rt"
  })
}

#rtb associations
resource "aws_route_table_association" "public" {
  count          = length(var.public_subnet_cidrs)
  subnet_id      = aws_subnet.public[count.index].id
  route_table_id = aws_route_table.public.id
}

#rtb associations private app
resource "aws_route_table_association" "private_app" {
  count          = length(var.private_app_subnet_cidrs)
  subnet_id      = aws_subnet.private_app[count.index].id
  route_table_id = aws_route_table.private_app.id
}

#rtb associations private db
resource "aws_route_table_association" "private_db" {
  count          = length(var.private_db_subnet_cidrs)
  subnet_id      = aws_subnet.private_db[count.index].id
  route_table_id = aws_route_table.private_db.id
}