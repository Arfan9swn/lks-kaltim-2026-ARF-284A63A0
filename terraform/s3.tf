# s3.tf

resource "aws_s3_bucket" "uploads" {
  bucket = var.s3_bucket_name

  tags = merge(var.common_tags, {
    Name = "${var.project_name}-uploads-bucket"
  })
}

# Enable versioning
resource "aws_s3_bucket_versioning" "uploads" {
  bucket = aws_s3_bucket.uploads.id
  versioning_configuration {
    status = "Enabled"
  }
}

#acls
resource "aws_s3_bucket_public_access_block" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

resource "aws_s3_bucket_server_side_encryption_configuration" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
  }
}

resource "aws_s3_bucket_lifecycle_configuration" "uploads" {
  bucket = aws_s3_bucket.uploads.id

  rule {
    id     = "archive-old-files"
    status = "Enabled"

    filter {
      prefix = ""
    }

    transition {
      days          = 30
      storage_class = "STANDARD_IA"
    }

    transition {
      days          = 90
      storage_class = "GLACIER"
    }

    expiration {
      days = 365
    }
  }
}

# CORS configuration is optional and commented out by default
# Uncomment and configure allowed_origins with your actual domain when needed
# resource "aws_s3_bucket_cors_configuration" "uploads" {
#   bucket = aws_s3_bucket.uploads.id
#
#   cors_rule {
#     id             = "allow-cross-origin"
#     allowed_headers = ["*"]
#     allowed_methods = ["GET", "PUT", "POST", "DELETE"]
#     allowed_origins = ["https://yourdomain.com"] # Replace with your actual domain
#     expose_headers  = ["ETag"]
#     max_age_seconds = 3000
#   }
# }
