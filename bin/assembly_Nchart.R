library(ggplot2)
library(MASS)
library(scales)


# args<-commandArgs(TRUE)
# prefix <- args[1]
# 

# TESTING:
prefix <- "/Applications/XAMPP/htdocs/Assemblytics/user_data/example3/SKBR3_breast_cancer_cell_line"


#############   MAKE EDITS TO EVERYTHING BELOW THIS LINE"




filename <- paste(prefix, ".coords.ref.genome", sep="")

genome.length=3000000000


plot.output.filename <- paste(filename, "Pacbio_vs_Illumina_contigs.png", sep="")

illumina.data <- read.csv(illumina.contigs.path, sep="\t", quote='', stringsAsFactors=FALSE,header=FALSE)
pacbio.data <- read.csv(pacbio.path, sep="\t", quote='', stringsAsFactors=FALSE,header=FALSE)
names(illumina.data) <- c("name","length")
names(pacbio.data) <- c("name","length")

illumina.plot <- data.frame(NG=cumsum(as.numeric(illumina.data$length/genome.length*100)),contig.length=as.numeric(illumina.data$length),contig.source="Illumina")
pacbio.plot <- data.frame(NG=cumsum(as.numeric(pacbio.data$length/genome.length*100)),contig.length=as.numeric(pacbio.data$length),contig.source="PacBio")
both.plot <- rbind(illumina.plot,pacbio.plot)



  
# png(file=plot.output.filename,width=800,height=800)

theme_set(theme_gray(base_size = 24))
ggplot(both.plot, aes(x = NG, y = contig.length,color=contig.source)) + xlim(0,100) + scale_y_log10(breaks = trans_breaks("log10", function(x) 10^x), labels = trans_format("log10", math_format(10^.x))) + geom_path(size=2) + labs(x = "NG(x)%",y="Contig length",colour="Assembly") + annotation_logticks(sides="lr") 

# dev.off()





