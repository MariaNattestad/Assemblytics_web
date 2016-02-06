library(ggplot2)

# args <- c("/Applications/XAMPP/htdocs/ABVC/user_data/example1/my_assembly") # TESTING

args<-commandArgs(TRUE)

output_prefix <- args[1]

filename <- paste(output_prefix,".ABVC_structural_variants.bed",sep="")

print(filename)
bed <- read.csv(filename, sep="\t", quote='', header=FALSE)

names(bed)[1:11] <- c("chrom","start","stop","name","size","strand","type","ref.dist","query.dist","contig_position","method.found")
head(bed)

types.allowed <- c("Insertion","Deletion","Repeat_expansion","Repeat_contraction","Tandem_expansion","Tandem_contraction")


summary(bed$type)

bed$type <- factor(bed$type, levels = types.allowed)

summary(bed$type)


print("Total small variants:")
print(nrow(bed))


to.file = TRUE

theme_set(theme_gray(base_size = 24))

head(bed)

summary(bed$type)

summary(bed$repeat.class)

head(bed)


# library(RColorBrewer)
# display.brewer.all()

color_palette_name <- "Set1"
binwidth <- 5


# png(paste(output_prefix,".2.png", sep=""),1000,1000)
# ggplot(bed,aes(x=ref.dist,y=query.dist,color=type)) + geom_point()+xlim(-10000,10000)+ylim(-10000,10000)+labs(x="Reference distance", y="Query distance",color="Variant type") + scale_color_brewer(palette=color_palette_name)
# dev.off()


png(paste(output_prefix,".plot.1.png", sep=""),1000,1000)
ggplot(bed,aes(x=size, fill=type)) + geom_bar(binwidth=binwidth*10) + scale_fill_brewer(palette=color_palette_name) + facet_grid(type ~ .) + labs(fill="Variant type",x="Variant size",y="Count") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()


png(paste(output_prefix,".plot.2.png", sep=""),1000,1000)
ggplot(bed,aes(x=size, fill=type)) + geom_bar(binwidth=binwidth) + scale_fill_brewer(palette=color_palette_name) + xlim(0,500) + facet_grid(type ~ .) + labs(fill="Variant type",x="Variant size",y="Count") + theme(strip.text=element_blank(),strip.background=element_blank())
dev.off()

 
# png(paste(output_prefix,".6.png", sep=""),1000,1000)
# ggplot(bed,aes(x=size, fill=type)) + geom_density(adjust=0.2) + scale_fill_brewer(palette=color_palette_name) + facet_grid(type ~ .) + scale_x_log10() + labs(fill="Variant type",x="Variant size",y="Count") + theme(strip.text=element_blank(),strip.background=element_blank())
# dev.off()

